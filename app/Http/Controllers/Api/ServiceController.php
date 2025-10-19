<?php

namespace App\Http\Controllers\Api;

use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends ApiController
{
    protected $model = Service::class;
    protected $cacheKey = 'services';

    public function __construct()
    {
        $this->cacheTtl = env('QUERY_CACHE_TTL', 300);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        try {
            $services = Service::where('is_active', true)
                ->orderBy('name')
                ->get();

            return $this->successResponse($services, 'Services retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve services: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'code' => 'required|string|max:50|unique:services',
                'name' => 'required|string|max:255',
                'description' => 'nullable|string',
                'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
                'price' => 'required|numeric|min:0',
                'duration_days' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $service = Service::create($request->all());

            return $this->successResponse($service, 'Service created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create service: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $service = Service::findOrFail($id);

            return $this->successResponse($service, 'Service retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Service not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        try {
            $service = Service::findOrFail($id);

            $request->validate([
                'code' => 'sometimes|string|max:50|unique:services,code,' . $id,
                'name' => 'sometimes|string|max:255',
                'description' => 'nullable|string',
                'service_type' => 'sometimes|in:konsultasi,ortosis,protesis,terapi,alat',
                'price' => 'sometimes|numeric|min:0',
                'duration_days' => 'nullable|integer|min:0',
                'is_active' => 'boolean',
            ]);

            $service->update($request->all());

            return $this->successResponse($service, 'Service updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update service: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $service = Service::findOrFail($id);
            $service->delete();

            return $this->successResponse(null, 'Service deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete service: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get active services
     */
    public function getActiveServices()
    {
        try {
            $services = Service::where('is_active', true)
                ->orderBy('name')
                ->get();

            return $this->successResponse($services, 'Active services retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve active services: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get services by type
     */
    public function getByType($type)
    {
        try {
            $services = Service::where('service_type', $type)
                ->where('is_active', true)
                ->orderBy('name')
                ->get();

            return $this->successResponse($services, "Services of type {$type} retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve services by type: ' . $e->getMessage(), 500);
        }
    }
}
