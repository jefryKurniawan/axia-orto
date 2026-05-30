<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class AdjustStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'type' => 'required|in:masuk,keluar,adjustment',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ];
    }
}
