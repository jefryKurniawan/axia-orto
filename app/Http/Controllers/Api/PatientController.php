<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use Illuminate\Http\Request;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\JsonResponse;

class PatientController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Ambil semua pasien, Anda bisa tambahkan paginasi di sini: Patient::paginate(10);
        $patients = Patient::all();

        return response()->json([
            'message' => 'Daftar pasien berhasil diambil.',
            'data' => $patients
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientRequest $request): JsonResponse
    {
        // Data sudah divalidasi oleh StorePatientRequest
        $data = $request->validated();

        // Optional: Tambahkan kolom audit (misalnya ID user yang sedang login)
        // $data['created_by'] = auth()->id();

        $patient = Patient::create($data);

        return response()->json([
            'message' => 'Data pasien berhasil ditambahkan.',
            'data' => $patient
        ], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Patient $patient): JsonResponse
    {
        // Route Model Binding memastikan $patient adalah instance Model Patient yang valid
        return response()->json([
            'message' => 'Detail pasien berhasil diambil.',
            'data' => $patient
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientRequest $request, Patient $patient): JsonResponse
    {
        // Data sudah divalidasi oleh UpdatePatientRequest
        $data = $request->validated();

        // Optional: Tambahkan kolom audit
        // $data['updated_by'] = auth()->id();

        $patient->update($data);

        return response()->json([
            'message' => 'Data pasien berhasil diperbarui.',
            'data' => $patient
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Patient $patient): JsonResponse
    {
        $patient->delete();

        return response()->json([
            'message' => 'Data pasien berhasil dihapus.'
        ], 204); // 204 No Content
    }
}
