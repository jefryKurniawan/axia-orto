<?php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\Request;

class PatientController extends ApiController
{
    protected $model = Patient::class;
    protected $cacheKey = 'patients';

    /**
     * Display a listing of the resource - TANPA CACHE
     */
    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');

            $query = Patient::with(['consultations']);

            if ($search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('medical_record_number', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            }

            $patients = $query->orderBy('created_at', 'desc')
                ->paginate($perPage);

            return $this->successResponse($patients, 'Patients retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve patients: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage - TANPA CACHE
     */
    public function store(StorePatientRequest $request)
    {
        try {
            $patient = Patient::create($request->validated());
            return $this->successResponse($patient, 'Patient created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create patient: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource - TANPA CACHE
     */
    public function show($id)
    {
        try {
            $patient = Patient::with([
                'consultations.doctor',
                'treatmentOrders',
                'measurements'
            ])->findOrFail($id);

            return $this->successResponse($patient, 'Patient retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Patient not found', 404);
        }
    }

    /**
     * Update the specified resource in storage - TANPA CACHE
     */
    public function update(UpdatePatientRequest $request, $id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->update($request->validated());
            return $this->successResponse($patient, 'Patient updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update patient: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage - TANPA CACHE
     */
    public function destroy($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete();
            return $this->successResponse(null, 'Patient deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete patient: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Stats - TANPA CACHE
     */
    public function stats()
    {
        try {
            $stats = [
                'total' => Patient::count(),
                'recent_30_days' => Patient::where('created_at', '>=', now()->subDays(30))->count(),
                'by_gender' => Patient::selectRaw('gender, count(*) as count')
                    ->groupBy('gender')
                    ->pluck('count', 'gender'),
                'by_insurance' => Patient::selectRaw('insurance_type, count(*) as count')
                    ->groupBy('insurance_type')
                    ->pluck('count', 'insurance_type'),
            ];

            return $this->successResponse($stats, 'Patient statistics retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Search - TANPA CACHE
     */
    public function search(Request $request)
    {
        try {
            $search = $request->get('q');

            if (!$search) {
                return $this->errorResponse('Search query is required', 400);
            }

            $patients = Patient::where('name', 'like', "%{$search}%")
                ->orWhere('medical_record_number', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%")
                ->limit(10)
                ->get(['id', 'medical_record_number', 'name', 'phone']);

            return $this->successResponse($patients, 'Search results retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage(), 500);
        }
    }
}
