<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Ambil ID pasien dari rute (route binding)
        $patientId = $this->route('patient')->id ?? null;

        return [
            // Pastikan unik, kecuali untuk data pasien yang sedang di-update
            'medical_record_number' => ['sometimes', 'required', 'string', 'max:20', Rule::unique('patients', 'medical_record_number')->ignore($patientId)],
            'nik' => ['sometimes', 'required', 'string', 'max:16', Rule::unique('patients', 'nik')->ignore($patientId)],
            'name' => 'sometimes|required|string|max:255',
            'date_of_birth' => 'sometimes|required|date|before:today',
            'gender' => ['sometimes', 'required', Rule::in(['L', 'P'])],
            'phone' => 'nullable|string|max:20',
            'email' => ['nullable', 'email', 'max:255', Rule::unique('patients', 'email')->ignore($patientId)],
            'address' => 'sometimes|required|string',
            // ... kolom lainnya menggunakan 'sometimes'
            'insurance_type' => ['nullable', Rule::in(['bpjs', 'mandiri', 'asuransi', 'lainnya'])],
            'blood_type' => ['nullable', Rule::in(['A', 'B', 'AB', 'O'])],
            'is_active' => 'nullable|boolean',
        ];
    }
}
