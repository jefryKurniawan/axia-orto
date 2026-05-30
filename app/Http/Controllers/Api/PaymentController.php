<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePaymentRequest;
use App\Http\Requests\UpdatePaymentRequest;
use App\Models\Payment;
use App\Models\TreatmentOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search') ?? '';
        $status = $request->input('status') ?? '';
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $cacheKey = CacheHelper::key('payments', 'list', [
            'page' => $page,
            'search' => $search,
            'status' => $status,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($search, $status, $perPage) {
            $query = DB::table('payments')
                ->join('treatment_orders', 'payments.treatment_order_id', '=', 'treatment_orders.id')
                ->join('patients', 'treatment_orders.patient_id', '=', 'patients.id')
                ->select(
                    'payments.id',
                    'payments.uuid',
                    'payments.payment_number',
                    'payments.payment_date',
                    'payments.amount',
                    'payments.payment_method',
                    'payments.status',
                    'treatment_orders.order_number',
                    'patients.name as patient_name',
                    'payments.created_at'
                );

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('payments.payment_number', 'like', "%{$search}%")
                      ->orWhere('treatment_orders.order_number', 'like', "%{$search}%")
                      ->orWhere('patients.name', 'like', "%{$search}%");
                });
            }

            if ($status !== '') {
                $query->where('payments.status', $status);
            }

            return $query->orderBy('payments.created_at', 'desc')->paginate($perPage);
        });

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }

    public function show(string $uuid): JsonResponse
    {
        $payment = Payment::with(['order.patient', 'createdBy'])
            ->where('uuid', $uuid)
            ->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $payment,
        ]);
    }

    public function store(StorePaymentRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $paymentNumber = 'PAY-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        $payment = Payment::create([
            'uuid' => Str::uuid(),
            'treatment_order_id' => $validated['treatment_order_id'],
            'payment_number' => $paymentNumber,
            'payment_date' => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
            'amount' => $validated['amount'],
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);

        CacheHelper::bumpVersion('payments');

        return response()->json([
            'success' => true,
            'data' => $payment->load(['order.patient']),
            'message' => 'Pembayaran berhasil dicatat.',
        ], 201);
    }

    public function update(UpdatePaymentRequest $request, string $uuid): JsonResponse
    {
        $payment = Payment::where('uuid', $uuid)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan.'], 404);
        }

        $validated = $request->validated();
        $payment->update($validated);

        CacheHelper::bumpVersion('payments');

        return response()->json([
            'success' => true,
            'data' => $payment->load(['order.patient']),
            'message' => 'Pembayaran berhasil diperbarui.',
        ]);
    }

    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $payment = Payment::where('uuid', $uuid)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan.'], 404);
        }

        $request->validate([
            'status' => 'required|in:pending,completed,failed,refunded',
        ]);

        $payment->update(['status' => $request->input('status')]);

        CacheHelper::bumpVersion('payments');

        return response()->json([
            'success' => true,
            'data' => $payment,
            'message' => 'Status pembayaran berhasil diperbarui.',
        ]);
    }

    public function byOrder(string $orderUuid): JsonResponse
    {
        $order = TreatmentOrder::where('uuid', $orderUuid)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        $payments = Payment::where('treatment_order_id', $order->id)
            ->with('createdBy')
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $payments,
        ]);
    }

    public function stats(): JsonResponse
    {
        $cacheKey = CacheHelper::key('payments', 'stats');

        $stats = CacheHelper::remember($cacheKey, 300, function () {
            return [
                'total_revenue' => (float) DB::table('payments')->where('status', 'completed')->sum('amount'),
                'pending_amount' => (float) DB::table('payments')->where('status', 'pending')->sum('amount'),
                'total_transactions' => (int) DB::table('payments')->count(),
                'completed_transactions' => (int) DB::table('payments')->where('status', 'completed')->count(),
                'by_method' => DB::table('payments')
                    ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as total'))
                    ->where('status', 'completed')
                    ->groupBy('payment_method')
                    ->get(),
            ];
        });

        return response()->json(['success' => true, 'data' => $stats]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $payment = Payment::where('uuid', $uuid)->first();

        if (!$payment) {
            return response()->json(['success' => false, 'message' => 'Pembayaran tidak ditemukan.'], 404);
        }

        if ($payment->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Pembayaran yang sudah selesai tidak bisa dihapus.'], 422);
        }

        $payment->delete();
        CacheHelper::bumpVersion('payments');

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dihapus.',
        ]);
    }
}
