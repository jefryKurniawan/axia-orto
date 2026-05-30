<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductionTrackingRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'step' => 'sometimes|string|max:100',
            'status' => 'sometimes|in:pending,in_progress,completed,cancelled',
            'assigned_to' => 'sometimes|exists:users,id',
            'notes' => 'nullable|string',
        ];
    }

    public function messages(): array
    {
        return [
            'status.in' => 'Status tidak valid.',
            'assigned_to.exists' => 'Teknisi tidak ditemukan.',
        ];
    }
}
