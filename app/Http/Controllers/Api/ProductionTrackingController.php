<?php
// app/Http/Controllers/Api/ProductionTrackingController.php

namespace App\Http\Controllers\Api;

use App\Models\ProductionTracking;
use App\Http\Requests\StoreProductionTrackingRequest;
use App\Http\Requests\UpdateProductionTrackingRequest;
use Illuminate\Http\Request;

class ProductionTrackingController extends ApiController
{
    protected $model = ProductionTracking::class;
    protected $cacheKey = 'production_tracking';

    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $orderId = $request->get('order_id');
            $stage = $request->get('stage');

            $cacheKey = "index.page.{$page}.order.{$orderId}.stage.{$stage}";

            $trackings = $this->cachedResponse($cacheKey, function () use ($orderId, $stage, $perPage) {
                $query = ProductionTracking::with(['order', 'completedBy']);

                if ($orderId) {
                    $query->where('order_id', $orderId);
                }

                if ($stage) {
                    $query->where('production_stage', $stage);
                }

                return $query->orderBy('created_at', 'desc')->paginate($perPage);
            }, ['ProductionTracking']);

            return $this->successResponse($trackings, 'Production tracking records retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve production tracking: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreProductionTrackingRequest $request)
    {
        try {
            $tracking = ProductionTracking::create($request->validated());
            $this->clearCache(['ProductionTracking', 'TreatmentOrder']);
            return $this->successResponse($tracking, 'Production tracking record created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create production tracking record: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $tracking = $this->cachedResponse("show.{$id}", function () use ($id) {
                return ProductionTracking::with(['order.patient', 'completedBy'])->findOrFail($id);
            }, ['ProductionTracking']);

            return $this->successResponse($tracking, 'Production tracking record retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Production tracking record not found', 404);
        }
    }

    public function update(UpdateProductionTrackingRequest $request, $id)
    {
        try {
            $tracking = ProductionTracking::findOrFail($id);
            $tracking->update($request->validated());
            $this->clearCache(['ProductionTracking', 'TreatmentOrder']);
            return $this->successResponse($tracking, 'Production tracking record updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update production tracking record: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $tracking = ProductionTracking::findOrFail($id);
            $tracking->delete();
            $this->clearCache(['ProductionTracking', 'TreatmentOrder']);
            return $this->successResponse(null, 'Production tracking record deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete production tracking record: ' . $e->getMessage(), 500);
        }
    }

    public function getCurrentStage($orderId)
    {
        try {
            $currentStage = $this->cachedResponse("current_stage.{$orderId}", function () use ($orderId) {
                return ProductionTracking::where('order_id', $orderId)
                    ->whereNull('completed_at')
                    ->latest()
                    ->first();
            }, ['ProductionTracking']);

            return $this->successResponse($currentStage, 'Current production stage retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve current production stage: ' . $e->getMessage(), 500);
        }
    }

    public function completeStage(Request $request, $id)
    {
        try {
            $request->validate([
                'completed_by' => 'required|exists:users,id',
                'notes' => 'nullable|string'
            ]);

            $tracking = ProductionTracking::findOrFail($id);
            $tracking->update([
                'completed_by' => $request->completed_by,
                'completed_at' => now(),
                'notes' => $request->notes
            ]);

            $this->clearCache(['ProductionTracking', 'TreatmentOrder']);

            return $this->successResponse($tracking, 'Production stage completed successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to complete production stage: ' . $e->getMessage(), 500);
        }
    }
}
