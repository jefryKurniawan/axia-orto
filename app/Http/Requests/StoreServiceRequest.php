<?php
// app/Http/Requests/StoreServiceRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreServiceRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => 'required|string|max:50|unique:services',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'service_type' => 'required|in:konsultasi,ortosis,protesis,terapi,alat',
            'price' => 'required|numeric|min:0',
            'duration_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }
}
