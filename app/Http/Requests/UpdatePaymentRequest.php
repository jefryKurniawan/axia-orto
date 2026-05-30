<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdatePaymentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'payment_date' => 'sometimes|date',
            'payment_method' => 'sometimes|in:cash,transfer,debit_card,credit_card',
            'amount' => 'sometimes|numeric|min:1',
            'status' => 'sometimes|in:pending,completed,failed,refunded',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'payment_method.in' => 'Metode pembayaran tidak valid.',
            'status.in' => 'Status tidak valid.',
            'amount.min' => 'Jumlah pembayaran minimal 1.',
        ];
    }
}
