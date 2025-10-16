<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PatientAttachment;
use Illuminate\Http\Request;
use App\Models\Patient; // Diperlukan untuk validasi relasi
use App\Http\Requests\StorePatientAttachmentRequest;
use App\Http\Requests\UpdatePatientAttachmentRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PatientAttachmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Patient $patient): JsonResponse
    {
        $attachments = $patient->patientAttachments()->latest()->get();

        return response()->json([
            'message' => 'Daftar lampiran pasien berhasil diambil.',
            'data' => $attachments
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StorePatientAttachmentRequest $request, Patient $patient): JsonResponse
    {
        $file = $request->file('attachment_file');

        // 1. Simpan File ke Storage
        // Path penyimpanan: attachments/patient_id/{uuid_baru}.{ext}
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();
        $filePath = $file->storeAs("attachments/{$patient->id}", $fileName, 'public'); // Simpan di storage/app/public/attachments

        if (!$filePath) {
            return response()->json(['message' => 'Gagal menyimpan file.'], 500);
        }

        // 2. Simpan Metadata ke Database
        $data = $request->validated();
        $attachment = $patient->patientAttachments()->create([
            'uuid' => Str::uuid(), // Generate UUID
            'patient_id' => $patient->id,
            'file_name' => $file->getClientOriginalName(),
            'file_path' => $filePath,
            'file_type' => $data['file_type'],
            'file_size' => $file->getSize(),
            'file_hash' => hash_file('sha256', Storage::disk('public')->path($filePath)), // Hitung hash file
            'description' => $data['description'] ?? null,
            'uploaded_by' => auth()->id() ?? 1, // Ganti 1 dengan ID user yang sedang login
        ]);

        return response()->json([
            'message' => 'Lampiran pasien berhasil diunggah dan ditambahkan.',
            'data' => $attachment
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(PatientAttachment $patientAttachment): JsonResponse
    {
        return response()->json([
            'message' => 'Detail lampiran berhasil diambil.',
            'data' => $patientAttachment
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdatePatientAttachmentRequest $request, PatientAttachment $patientAttachment): JsonResponse
    {
        $patientAttachment->update($request->validated());

        return response()->json([
            'message' => 'Metadata lampiran berhasil diperbarui.',
            'data' => $patientAttachment
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(PatientAttachment $patientAttachment): JsonResponse
    {
        // 1. Hapus File dari Storage
        Storage::disk('public')->delete($patientAttachment->file_path);

        // 2. Hapus Metadata dari Database
        $patientAttachment->delete();

        return response()->json([
            'message' => 'Lampiran pasien berhasil dihapus.'
        ], 204);
    }
}
