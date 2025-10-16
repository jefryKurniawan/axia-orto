<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateProductCategoryRequest extends FormRequest
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
        // Ambil ID kategori dari rute
        $categoryId = $this->route('product_category')->id ?? null;

        return [
            // Nama harus unik, kecuali untuk kategori yang sedang di-update
            'name' => [
                'sometimes', // Hanya validasi jika dikirim
                'required',
                'string',
                'max:255',
                Rule::unique('product_categories', 'name')->ignore($categoryId),
            ],
            'description' => 'nullable|string',
            // parent_id tidak boleh merujuk ke dirinya sendiri, dan harus ada di tabel
            'parent_id' => [
                'nullable',
                'integer',
                'exists:product_categories,id',
                // Aturan pencegah rekursi tak terbatas: parent_id tidak boleh sama dengan id kategori saat ini
                Rule::notIn([$categoryId]),
            ],
            'is_active' => 'nullable|boolean',
        ];
    }
}
