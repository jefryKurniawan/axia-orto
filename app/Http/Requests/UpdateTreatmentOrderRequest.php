<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTreatmentOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'sometimes|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'order_date' => 'sometimes|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'services' => 'sometimes|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.specifications' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.exists' => 'Pasien tidak ditemukan.',
            'services.min' => 'Minimal satu layanan harus dipilih.',
            'services.*.service_id.exists' => 'Layanan tidak ditemukan.',
            'services.*.quantity.min' => 'Jumlah minimal 1.',
        ];
    }
}
