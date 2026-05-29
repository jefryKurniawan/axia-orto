<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreConsultationRequest;
use App\Models\Consultation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsultationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search') ?? '';
        $status = $request->input('status') ?? '';
        $page = (int) $request->input('page', 1);

        $cacheKey = CacheHelper::key('consultations', 'list', [
            'page' => $page,
            'search' => $search,
            'status' => $status,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($search, $status) {
            $query = DB::table('consultations')
                ->join('patients', 'consultations.patient_id', '=', 'patients.id')
                ->join('users', 'consultations.doctor_id', '=', 'users.id')
                ->select(
                    'consultations.id', 'consultations.uuid', 'consultations.consultation_date',
                    'consultations.complaint', 'consultations.diagnosis', 'consultations.status',
                    'patients.name as patient_name', 'patients.medical_record_number',
                    'users.name as doctor_name'
                );

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('patients.name', 'like', "%{$search}%")
                      ->orWhere('patients.medical_record_number', 'like', "%{$search}%")
                      ->orWhere('consultations.diagnosis', 'like', "%{$search}%");
                });
            }

            if ($status !== '') {
                $query->where('consultations.status', $status);
            }

            return $query->orderBy('consultations.consultation_date', 'desc')->paginate(15);
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
        $consultation = DB::table('consultations')
            ->join('patients', 'consultations.patient_id', '=', 'patients.id')
            ->join('users', 'consultations.doctor_id', '=', 'users.id')
            ->where('consultations.uuid', $uuid)
            ->select(
                'consultations.*',
                'patients.name as patient_name', 'patients.medical_record_number',
                'users.name as doctor_name'
            )
            ->first();

        if (!$consultation) {
            return response()->json(['success' => false, 'message' => 'Konsultasi tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $consultation]);
    }

    public function store(StoreConsultationRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $consultation = Consultation::create($validated);

        CacheHelper::bumpVersion('consultations');

        return response()->json([
            'success' => true,
            'data' => $consultation,
            'message' => 'Konsultasi berhasil ditambahkan.',
        ], 201);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $consultation = Consultation::where('uuid', $uuid)->first();

        if (!$consultation) {
            return response()->json(['success' => false, 'message' => 'Konsultasi tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'patient_id' => 'sometimes|required|exists:patients,id',
            'doctor_id' => 'sometimes|required|exists:users,id',
            'consultation_date' => 'sometimes|required|date',
            'complaint' => 'sometimes|required|string',
            'diagnosis' => 'nullable|string',
            'treatment_plan' => 'nullable|string',
            'notes' => 'nullable|string',
            'follow_up_date' => 'nullable|date',
            'status' => 'sometimes|required|in:scheduled,in_progress,completed,cancelled',
        ]);

        $consultation->update($validated);
        CacheHelper::bumpVersion('consultations');

        return response()->json([
            'success' => true,
            'data' => $consultation,
            'message' => 'Konsultasi berhasil diperbarui.',
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $consultation = Consultation::where('uuid', $uuid)->first();

        if (!$consultation) {
            return response()->json(['success' => false, 'message' => 'Konsultasi tidak ditemukan.'], 404);
        }

        $consultation->delete();
        CacheHelper::bumpVersion('consultations');

        return response()->json(['success' => true, 'message' => 'Konsultasi berhasil dihapus.']);
    }

    public function today(): JsonResponse
    {
        $cacheKey = CacheHelper::key('consultations', 'today');

        $consultations = CacheHelper::remember($cacheKey, 60, function () {
            return DB::table('consultations')
                ->join('patients', 'consultations.patient_id', '=', 'patients.id')
                ->join('users', 'consultations.doctor_id', '=', 'users.id')
                ->whereDate('consultations.consultation_date', today())
                ->select(
                    'consultations.uuid', 'consultations.consultation_date',
                    'consultations.complaint', 'consultations.status',
                    'patients.name as patient_name',
                    'users.name as doctor_name'
                )
                ->orderBy('consultations.consultation_date')
                ->get();
        });

        return response()->json(['success' => true, 'data' => $consultations]);
    }
}
