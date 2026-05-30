<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'treatment_order_id' => 'required|exists:treatment_orders,id',
            'payment_date' => 'required|date',
            'payment_method' => 'required|in:cash,transfer,debit_card,credit_card',
            'amount' => 'required|numeric|min:1',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'treatment_order_id.required' => 'Order harus dipilih.',
            'treatment_order_id.exists' => 'Order tidak ditemukan.',
            'payment_date.required' => 'Tanggal pembayaran harus diisi.',
            'payment_method.required' => 'Metode pembayaran harus dipilih.',
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'amount.required' => 'Jumlah pembayaran harus diisi.',
            'amount.min' => 'Jumlah pembayaran minimal 1.',
        ];
    }
}
