<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductionTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_order_id' => 'required|exists:treatment_orders,id',
            'step' => 'required|string|max:100',
            'assigned_to' => 'required|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'treatment_order_id.required' => 'Order harus dipilih.',
            'treatment_order_id.exists' => 'Order tidak ditemukan.',
            'step.required' => 'Langkah produksi harus diisi.',
            'assigned_to.required' => 'Teknisi harus dipilih.',
            'assigned_to.exists' => 'Teknisi tidak ditemukan.',
        ];
    }
}
