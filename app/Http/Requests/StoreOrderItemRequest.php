<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_order_id' => 'required|exists:treatment_orders,id',
            'service_id' => 'required|exists:services,id',
            'quantity' => 'required|integer|min:1',
            'unit_price' => 'required|numeric|min:0',
            'specifications' => 'nullable|array',
        ];
    }

    public function messages(): array
    {
        return [
            'treatment_order_id.required' => 'Order harus dipilih.',
            'treatment_order_id.exists' => 'Order tidak ditemukan.',
            'service_id.required' => 'Layanan harus dipilih.',
            'service_id.exists' => 'Layanan tidak ditemukan.',
            'quantity.required' => 'Jumlah harus diisi.',
            'quantity.min' => 'Jumlah minimal 1.',
            'unit_price.required' => 'Harga satuan harus diisi.',
            'unit_price.min' => 'Harga satuan tidak boleh negatif.',
        ];
    }
}
