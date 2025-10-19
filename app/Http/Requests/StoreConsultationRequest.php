<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreConsultationRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'consultation_date' => 'required|date|after_or_equal:today',
            'complaint' => 'required|string|min:10|max:1000',
            'diagnosis' => 'required|string|min:10|max:1000',
            'treatment_plan' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'follow_up_date' => 'nullable|date|after:consultation_date',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'patient_id.required' => 'Pasien harus dipilih.',
            'patient_id.exists' => 'Pasien yang dipilih tidak valid.',
            'doctor_id.required' => 'Dokter harus dipilih.',
            'doctor_id.exists' => 'Dokter yang dipilih tidak valid.',
            'consultation_date.required' => 'Tanggal konsultasi harus diisi.',
            'consultation_date.after_or_equal' => 'Tanggal konsultasi tidak boleh di masa lalu.',
            'complaint.required' => 'Keluhan pasien harus diisi.',
            'diagnosis.required' => 'Diagnosis harus diisi.',
        ];
    }
}
