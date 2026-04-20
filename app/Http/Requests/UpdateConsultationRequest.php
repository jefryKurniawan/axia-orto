<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateConsultationRequest extends FormRequest
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
            'patient_id' => 'sometimes|exists:patients,id',
            'doctor_id' => 'sometimes|exists:users,id',
            'consultation_date' => 'sometimes|date',
            'complaint' => 'sometimes|string|min:10|max:1000',
            'diagnosis' => 'sometimes|string|min:10|max:1000',
            'treatment_plan' => 'nullable|string|max:2000',
            'notes' => 'nullable|string|max:1000',
            'follow_up_date' => 'nullable|date|after:consultation_date',
            'status' => 'sometimes|in:scheduled,in_progress,completed,cancelled',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'patient_id.exists' => 'Pasien yang dipilih tidak valid.',
            'doctor_id.exists' => 'Dokter yang dipilih tidak valid.',
            'consultation_date.date' => 'Format tanggal konsultasi tidak valid.',
            'follow_up_date.after' => 'Tanggal follow up harus setelah tanggal konsultasi.',
        ];
    }
}