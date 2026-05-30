<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateInventoryItemRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'code' => ['required', 'string', 'max:50', Rule::unique('inventory_items')->ignore($this->route('uuid'), 'uuid')],
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'category' => 'required|in:bahan_baku,komponen,alat_jadi',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|integer|min:0',
            'reorder_level' => 'required|integer|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ];
    }
}
