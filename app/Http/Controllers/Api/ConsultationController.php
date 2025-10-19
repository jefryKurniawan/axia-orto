<?php

namespace App\Http\Controllers\Api;

use App\Models\Consultation;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use Illuminate\Http\Request;

class ConsultationController extends ApiController
{
    protected $model = Consultation::class;
    protected $cacheKey = 'consultations';

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
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');
            $doctorId = $request->get('doctor_id');
            $date = $request->get('date');

            $query = Consultation::with(['patient', 'doctor']);

            if ($status) {
                $query->where('status', $status);
            }

            if ($doctorId) {
                $query->where('doctor_id', $doctorId);
            }

            if ($date) {
                $query->whereDate('consultation_date', $date);
            }

            $consultations = $query->orderBy('consultation_date', 'desc')
                ->paginate($perPage);

            return $this->successResponse($consultations, 'Consultations retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve consultations: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreConsultationRequest $request)
    {
        try {
            $consultation = Consultation::create($request->validated());
            return $this->successResponse($consultation, 'Consultation created successfully', 201);
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create consultation: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        try {
            $consultation = Consultation::with(['patient', 'doctor', 'measurements'])
                ->findOrFail($id);

            return $this->successResponse($consultation, 'Consultation retrieved successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Consultation not found', 404);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateConsultationRequest $request, $id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->update($request->validated());
            return $this->successResponse($consultation, 'Consultation updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update consultation: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Update consultation status
     */
    public function updateStatus(Request $request, $id)
    {
        try {
            $request->validate([
                'status' => 'required|in:scheduled,in_progress,completed,cancelled'
            ]);

            $consultation = Consultation::findOrFail($id);
            $consultation->update(['status' => $request->status]);

            return $this->successResponse($consultation, 'Consultation status updated successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update consultation status: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $consultation = Consultation::findOrFail($id);
            $consultation->delete();
            return $this->successResponse(null, 'Consultation deleted successfully');
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete consultation: ' . $e->getMessage(), 500);
        }
    }

    /**
     * Get today's schedule
     */
    public function todaySchedule(Request $request)
    {
        try {
            $doctorId = $request->get('doctor_id');

            $query = Consultation::with(['patient'])
                ->whereDate('consultation_date', today())
                ->whereIn('status', ['scheduled', 'in_progress']);

            if ($doctorId) {
                $query->where('doctor_id', $doctorId);
            }

            $schedule = $query->orderBy('consultation_date')->get();

            return $this->successResponse($schedule, "Today's schedule retrieved successfully");
        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve schedule: ' . $e->getMessage(), 500);
        }
    }
}
