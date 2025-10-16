<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductRequest extends FormRequest
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
        // Ambil ID produk dari rute
        $productId = $this->route('product')->id ?? null;

        return [
            'category_id' => 'sometimes|required|exists:product_categories,id',
            // SKU harus unik, kecuali untuk produk saat ini
            'sku' => [
                'sometimes',
                'required',
                'string',
                'max:50',
                Rule::unique('products', 'sku')->ignore($productId),
            ],
            'name' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string',
            'product_type' => ['sometimes', 'required', Rule::in(['ortosis', 'protesis', 'alat_bantu', 'konsultasi', 'terapi'])],

            'unit_price' => 'sometimes|required|numeric|min:0',
            'cost_price' => 'sometimes|required|numeric|min:0',

            'is_taxable' => 'nullable|boolean',
            'tax_rate' => 'nullable|numeric|min:0|max:100',
            'is_active' => 'nullable|boolean',
            'specifications' => 'nullable|json',
            'manufacturing_time' => 'nullable|integer|min:0',
        ];
    }
}
