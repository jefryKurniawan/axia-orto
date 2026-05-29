<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\StorePatientRequest;
use App\Models\Patient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PatientController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search') ?? '';
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $cacheKey = CacheHelper::key('patients', 'list', [
            'page' => $page,
            'search' => $search,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($search, $perPage) {
            $query = DB::table('patients')
                ->select('id', 'uuid', 'medical_record_number', 'name', 'date_of_birth', 'gender', 'phone', 'insurance_type', 'created_at');

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('medical_record_number', 'like', "%{$search}%")
                      ->orWhere('nik', 'like', "%{$search}%")
                      ->orWhere('phone', 'like', "%{$search}%");
                });
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
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
        $cacheKey = CacheHelper::key('patients', 'show', ['uuid' => $uuid]);

        $patient = CacheHelper::remember($cacheKey, 300, function () use ($uuid) {
            return DB::table('patients')->where('uuid', $uuid)->first();
        });

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.'], 404);
        }

        return response()->json(['success' => true, 'data' => $patient]);
    }

    public function store(StorePatientRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $mrn = 'MRN-' . date('Ymd') . '-' . str_pad(Patient::count() + 1, 4, '0', STR_PAD_LEFT);

        $patient = Patient::create(array_merge($validated, [
            'uuid' => (string) \Illuminate\Support\Str::uuid(),
            'medical_record_number' => $mrn,
        ]));

        CacheHelper::bumpVersion('patients');

        return response()->json([
            'success' => true,
            'data' => $patient,
            'message' => 'Pasien berhasil ditambahkan.',
        ], 201);
    }

    public function update(Request $request, string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.'], 404);
        }

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'nik' => 'nullable|string|size:16',
            'date_of_birth' => 'sometimes|required|date|before:today',
            'gender' => 'sometimes|required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:100',
            'insurance_type' => 'sometimes|required|in:bpjs,mandiri,asuransi',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'allergies' => 'nullable|string',
        ]);

        $patient->update($validated);
        CacheHelper::bumpVersion('patients');

        return response()->json([
            'success' => true,
            'data' => $patient,
            'message' => 'Pasien berhasil diperbarui.',
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $patient = Patient::where('uuid', $uuid)->first();

        if (!$patient) {
            return response()->json(['success' => false, 'message' => 'Pasien tidak ditemukan.'], 404);
        }

        if ($patient->hasActiveOrders()) {
            return response()->json(['success' => false, 'message' => 'Pasien memiliki order aktif.'], 422);
        }

        $patient->delete();
        CacheHelper::bumpVersion('patients');

        return response()->json(['success' => true, 'message' => 'Pasien berhasil dihapus.']);
    }

    public function stats(): JsonResponse
    {
        $cacheKey = CacheHelper::key('patients', 'stats');

        $stats = CacheHelper::remember($cacheKey, 3600, function () {
            return [
                'total' => DB::table('patients')->count(),
                'this_month' => DB::table('patients')
                    ->whereMonth('created_at', now()->month)
                    ->whereYear('created_at', now()->year)
                    ->count(),
                'by_gender' => DB::table('patients')
                    ->selectRaw('gender, COUNT(*) as count')
                    ->groupBy('gender')
                    ->pluck('count', 'gender'),
                'by_insurance' => DB::table('patients')
                    ->selectRaw('insurance_type, COUNT(*) as count')
                    ->groupBy('insurance_type')
                    ->pluck('count', 'insurance_type'),
            ];
        });

        return response()->json(['success' => true, 'data' => $stats]);
    }
}
