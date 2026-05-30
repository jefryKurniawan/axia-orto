<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTreatmentOrderRequest;
use App\Http\Requests\UpdateTreatmentOrderRequest;
use App\Models\OrderItem;
use App\Models\TreatmentOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search') ?? '';
        $status = $request->input('status') ?? '';
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $cacheKey = CacheHelper::key('orders', 'list', [
            'page' => $page,
            'search' => $search,
            'status' => $status,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($search, $status, $perPage) {
            $query = DB::table('treatment_orders')
                ->join('patients', 'treatment_orders.patient_id', '=', 'patients.id')
                ->leftJoin('users', 'treatment_orders.created_by', '=', 'users.id')
                ->select(
                    'treatment_orders.id',
                    'treatment_orders.uuid',
                    'treatment_orders.order_number',
                    'treatment_orders.order_date',
                    'treatment_orders.delivery_date',
                    'treatment_orders.status',
                    'treatment_orders.total_amount',
                    'patients.name as patient_name',
                    'patients.medical_record_number',
                    'users.name as created_by_name',
                    'treatment_orders.created_at'
                );

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('treatment_orders.order_number', 'like', "%{$search}%")
                      ->orWhere('patients.name', 'like', "%{$search}%")
                      ->orWhere('patients.medical_record_number', 'like', "%{$search}%");
                });
            }

            if ($status !== '') {
                $query->where('treatment_orders.status', $status);
            }

            return $query->orderBy('treatment_orders.created_at', 'desc')->paginate($perPage);
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
        $order = TreatmentOrder::with(['patient', 'consultation', 'orderItems.service', 'createdBy', 'payments', 'productionTrackings.assignedTo'])
            ->where('uuid', $uuid)
            ->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    public function store(StoreTreatmentOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $orderNumber = 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(4));

        $order = DB::transaction(function () use ($validated, $orderNumber) {
            $order = TreatmentOrder::create([
                'uuid' => Str::uuid(),
                'order_number' => $orderNumber,
                'patient_id' => $validated['patient_id'],
                'consultation_id' => $validated['consultation_id'] ?? null,
                'order_date' => $validated['order_date'],
                'delivery_date' => $validated['delivery_date'] ?? null,
                'status' => 'draft',
                'total_amount' => 0,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            $totalAmount = 0;
            foreach ($validated['services'] as $item) {
                $service = DB::table('services')->where('id', $item['service_id'])->first();
                $unitPrice = $service->price;
                $totalPrice = $unitPrice * $item['quantity'];

                OrderItem::create([
                    'treatment_order_id' => $order->id,
                    'service_id' => $item['service_id'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $unitPrice,
                    'total_price' => $totalPrice,
                    'specifications' => $item['specifications'] ?? null,
                ]);

                $totalAmount += $totalPrice;
            }

            $order->update(['total_amount' => $totalAmount]);

            return $order;
        });

        CacheHelper::bumpVersion('orders');

        return response()->json([
            'success' => true,
            'data' => $order->load(['patient', 'orderItems.service']),
            'message' => 'Order berhasil dibuat.',
        ], 201);
    }

    public function update(UpdateTreatmentOrderRequest $request, string $uuid): JsonResponse
    {
        $order = TreatmentOrder::where('uuid', $uuid)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        $validated = $request->validated();

        DB::transaction(function () use ($order, $validated) {
            $orderData = collect($validated)->except('services')->toArray();
            if (!empty($orderData)) {
                $order->update($orderData);
            }

            if (isset($validated['services'])) {
                $order->orderItems()->delete();

                $totalAmount = 0;
                foreach ($validated['services'] as $item) {
                    $service = DB::table('services')->where('id', $item['service_id'])->first();
                    $unitPrice = $service->price;
                    $totalPrice = $unitPrice * $item['quantity'];

                    OrderItem::create([
                        'treatment_order_id' => $order->id,
                        'service_id' => $item['service_id'],
                        'quantity' => $item['quantity'],
                        'unit_price' => $unitPrice,
                        'total_price' => $totalPrice,
                        'specifications' => $item['specifications'] ?? null,
                    ]);

                    $totalAmount += $totalPrice;
                }

                $order->update(['total_amount' => $totalAmount]);
            }
        });

        CacheHelper::bumpVersion('orders');

        return response()->json([
            'success' => true,
            'data' => $order->load(['patient', 'orderItems.service']),
            'message' => 'Order berhasil diperbarui.',
        ]);
    }

    public function updateStatus(Request $request, string $uuid): JsonResponse
    {
        $order = TreatmentOrder::where('uuid', $uuid)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        $request->validate([
            'status' => 'required|in:draft,confirmed,production,ready,delivered,cancelled',
        ]);

        $order->update(['status' => $request->input('status')]);

        CacheHelper::bumpVersion('orders');

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Status order berhasil diperbarui.',
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $order = TreatmentOrder::where('uuid', $uuid)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        if ($order->status !== 'draft') {
            return response()->json(['success' => false, 'message' => 'Hanya order dengan status draft yang bisa dihapus.'], 422);
        }

        $order->delete();
        CacheHelper::bumpVersion('orders');

        return response()->json([
            'success' => true,
            'message' => 'Order berhasil dihapus.',
        ]);
    }

    public function stats(): JsonResponse
    {
        $cacheKey = CacheHelper::key('orders', 'stats');

        $stats = CacheHelper::remember($cacheKey, 300, function () {
            return [
                'total' => DB::table('treatment_orders')->count(),
                'draft' => DB::table('treatment_orders')->where('status', 'draft')->count(),
                'confirmed' => DB::table('treatment_orders')->where('status', 'confirmed')->count(),
                'production' => DB::table('treatment_orders')->where('status', 'production')->count(),
                'ready' => DB::table('treatment_orders')->where('status', 'ready')->count(),
                'delivered' => DB::table('treatment_orders')->where('status', 'delivered')->count(),
                'cancelled' => DB::table('treatment_orders')->where('status', 'cancelled')->count(),
                'total_revenue' => (float) DB::table('treatment_orders')->where('status', 'delivered')->sum('total_amount'),
            ];
        });

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
