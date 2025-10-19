<?php
// app/Http/Controllers/Api/OrderItemController.php

namespace App\Http\Controllers\Api;

use App\Models\OrderItem;
use App\Http\Requests\StoreOrderItemRequest;
use App\Http\Requests\UpdateOrderItemRequest;
use Illuminate\Http\Request;

class OrderItemController extends ApiController
{
    protected $model = OrderItem::class;
    protected $cacheKey = 'order_items';

    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $orderId = $request->get('order_id');

            $cacheKey = "index.page.{$page}.order.{$orderId}";

            $orderItems = $this->cachedResponse($cacheKey, function () use ($orderId, $perPage) {
                $query = OrderItem::with(['order', 'service']);

                if ($orderId) {
                    $query->where('order_id', $orderId);
                }

                return $query->orderBy('created_at', 'desc')->paginate($perPage);
            }, ['OrderItem']);

            return $this->successResponse($orderItems, 'Order items retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve order items: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreOrderItemRequest $request)
    {
        try {
            $orderItem = OrderItem::create($request->validated());
            $this->clearCache(['OrderItem', 'TreatmentOrder']);
            return $this->successResponse($orderItem, 'Order item created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create order item: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $orderItem = $this->cachedResponse("show.{$id}", function () use ($id) {
                return OrderItem::with(['order.patient', 'service'])->findOrFail($id);
            }, ['OrderItem']);

            return $this->successResponse($orderItem, 'Order item retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Order item not found', 404);
        }
    }

    public function update(UpdateOrderItemRequest $request, $id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);
            $orderItem->update($request->validated());
            $this->clearCache(['OrderItem', 'TreatmentOrder']);
            return $this->successResponse($orderItem, 'Order item updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update order item: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $orderItem = OrderItem::findOrFail($id);
            $orderItem->delete();
            $this->clearCache(['OrderItem', 'TreatmentOrder']);
            return $this->successResponse(null, 'Order item deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete order item: ' . $e->getMessage(), 500);
        }
    }

    public function getByOrder($orderId)
    {
        try {
            $orderItems = $this->cachedResponse("order.{$orderId}", function () use ($orderId) {
                return OrderItem::where('order_id', $orderId)
                    ->with(['service'])
                    ->get();
            }, ['OrderItem']);

            return $this->successResponse($orderItems, 'Order items retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve order items: ' . $e->getMessage(), 500);
        }
    }
}
