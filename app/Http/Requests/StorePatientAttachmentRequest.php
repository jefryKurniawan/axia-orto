<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientAttachmentRequest extends FormRequest
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
            // patient_id akan diisi dari rute (nested resource) atau langsung dari payload
            'patient_id' => 'required|exists:patients,id',

            // Aturan untuk upload file yang sebenarnya (file_path, file_hash akan diisi setelah upload sukses)
            'attachment_file' => 'required|file|max:10240|mimes:jpeg,png,jpg,pdf,doc,docx', // Max 10MB, tipe yang diizinkan

            'file_type' => ['required', Rule::in(['ktp', 'bpjs', 'surat_rujukan', 'foto', 'xray', 'lainnya'])],
            'description' => 'nullable|string|max:500',
        ];
    }
}
