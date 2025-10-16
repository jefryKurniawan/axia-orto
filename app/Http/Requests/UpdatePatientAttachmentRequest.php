<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientAttachmentRequest extends FormRequest
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
            'file_type' => ['sometimes', 'required', Rule::in(['ktp', 'bpjs', 'surat_rujukan', 'foto', 'xray', 'lainnya'])],
            'description' => 'nullable|string|max:500',
            // File fisik tidak di-update/ganti di sini, melainkan melalui method yang berbeda jika diperlukan.
        ];
    }
}
