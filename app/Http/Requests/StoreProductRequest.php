<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreProductRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'category_id' => 'required|exists:product_categories,id',
            'sku' => 'required|string|max:50|unique:products,sku',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'product_type' => ['required', Rule::in(['ortosis', 'protesis', 'alat_bantu', 'konsultasi', 'terapi'])],

            // Aturan untuk tipe data numerik (Decimal)
            'unit_price' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',

            'is_taxable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100', // Persentase maksimum 100%
            'is_active' => 'nullable|boolean',

            // Spesifikasi JSON harus berupa array atau valid string JSON
            'specifications' => 'nullable|json',

            'manufacturing_time' => 'nullable|integer|min:0',
        ];
    }
}
