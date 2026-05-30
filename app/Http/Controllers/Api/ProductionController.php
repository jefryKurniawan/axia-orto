<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProductionTrackingRequest;
use App\Http\Requests\UpdateProductionTrackingRequest;
use App\Models\ProductionTracking;
use App\Models\TreatmentOrder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductionController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $orderUuid = $request->input('order_uuid') ?? '';
        $status = $request->input('status') ?? '';
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $cacheKey = CacheHelper::key('production', 'list', [
            'page' => $page,
            'order_uuid' => $orderUuid,
            'status' => $status,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($orderUuid, $status, $perPage) {
            $query = DB::table('production_trackings')
                ->join('treatment_orders', 'production_trackings.treatment_order_id', '=', 'treatment_orders.id')
                ->join('patients', 'treatment_orders.patient_id', '=', 'patients.id')
                ->join('users', 'production_trackings.assigned_to', '=', 'users.id')
                ->select(
                    'production_trackings.id',
                    'production_trackings.uuid',
                    'production_trackings.step',
                    'production_trackings.status',
                    'production_trackings.started_at',
                    'production_trackings.completed_at',
                    'treatment_orders.order_number',
                    'treatment_orders.uuid as order_uuid',
                    'patients.name as patient_name',
                    'users.name as assigned_to_name',
                    'production_trackings.created_at'
                );

            if ($orderUuid !== '') {
                $query->where('treatment_orders.uuid', $orderUuid);
            }

            if ($status !== '') {
                $query->where('production_trackings.status', $status);
            }

            return $query->orderBy('production_trackings.created_at', 'desc')->paginate($perPage);
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
        $tracking = ProductionTracking::with(['order.patient', 'assignedTo', 'completedBy'])
            ->where('uuid', $uuid)
            ->first();

        if (!$tracking) {
            return response()->json(['success' => false, 'message' => 'Tracking produksi tidak ditemukan.'], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $tracking,
        ]);
    }

    public function store(StoreProductionTrackingRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $tracking = ProductionTracking::create([
            'uuid' => Str::uuid(),
            'treatment_order_id' => $validated['treatment_order_id'],
            'step' => $validated['step'],
            'status' => 'pending',
            'assigned_to' => $validated['assigned_to'],
            'notes' => $validated['notes'] ?? null,
        ]);

        CacheHelper::bumpVersion('production');

        return response()->json([
            'success' => true,
            'data' => $tracking->load(['order.patient', 'assignedTo']),
            'message' => 'Tracking produksi berhasil dibuat.',
        ], 201);
    }

    public function update(UpdateProductionTrackingRequest $request, string $uuid): JsonResponse
    {
        $tracking = ProductionTracking::where('uuid', $uuid)->first();

        if (!$tracking) {
            return response()->json(['success' => false, 'message' => 'Tracking produksi tidak ditemukan.'], 404);
        }

        $validated = $request->validated();

        // Auto-set timestamps based on status changes
        if (isset($validated['status'])) {
            if ($validated['status'] === 'in_progress' && !$tracking->started_at) {
                $validated['started_at'] = now();
            }
            if ($validated['status'] === 'completed' && !$tracking->completed_at) {
                $validated['completed_at'] = now();
                $validated['completed_by'] = auth()->id();
            }
        }

        $tracking->update($validated);

        CacheHelper::bumpVersion('production');

        return response()->json([
            'success' => true,
            'data' => $tracking->load(['order.patient', 'assignedTo', 'completedBy']),
            'message' => 'Tracking produksi berhasil diperbarui.',
        ]);
    }

    public function byOrder(string $orderUuid): JsonResponse
    {
        $order = TreatmentOrder::where('uuid', $orderUuid)->first();

        if (!$order) {
            return response()->json(['success' => false, 'message' => 'Order tidak ditemukan.'], 404);
        }

        $trackings = ProductionTracking::where('treatment_order_id', $order->id)
            ->with(['assignedTo', 'completedBy'])
            ->orderBy('created_at')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $trackings,
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $tracking = ProductionTracking::where('uuid', $uuid)->first();

        if (!$tracking) {
            return response()->json(['success' => false, 'message' => 'Tracking produksi tidak ditemukan.'], 404);
        }

        if ($tracking->status === 'completed') {
            return response()->json(['success' => false, 'message' => 'Tracking yang sudah selesai tidak bisa dihapus.'], 422);
        }

        $tracking->delete();
        CacheHelper::bumpVersion('production');

        return response()->json([
            'success' => true,
            'message' => 'Tracking produksi berhasil dihapus.',
        ]);
    }
}
