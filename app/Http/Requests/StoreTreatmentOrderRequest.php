<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTreatmentOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'patient_id' => 'required|exists:patients,id',
            'consultation_id' => 'nullable|exists:consultations,id',
            'order_date' => 'required|date',
            'delivery_date' => 'nullable|date|after_or_equal:order_date',
            'notes' => 'nullable|string',
            'services' => 'required|array|min:1',
            'services.*.service_id' => 'required|exists:services,id',
            'services.*.quantity' => 'required|integer|min:1',
            'services.*.specifications' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'patient_id.required' => 'Pasien harus dipilih.',
            'patient_id.exists' => 'Pasien tidak ditemukan.',
            'order_date.required' => 'Tanggal order harus diisi.',
            'services.required' => 'Minimal satu layanan harus dipilih.',
            'services.min' => 'Minimal satu layanan harus dipilih.',
            'services.*.service_id.required' => 'Layanan harus dipilih.',
            'services.*.service_id.exists' => 'Layanan tidak ditemukan.',
            'services.*.quantity.required' => 'Jumlah harus diisi.',
            'services.*.quantity.min' => 'Jumlah minimal 1.',
        ];
    }
}
