<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePatientRequest extends FormRequest
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
     */
    public function rules(): array
    {
        return [
            'medical_record_number' => [
                'sometimes',
                'string',
                'max:20',
                Rule::unique('patients')->ignore($this->patient)
            ],
            'name' => 'sometimes|string|max:255',
            'nik' => 'nullable|string|size:16',
            'date_of_birth' => 'sometimes|date|before:today',
            'gender' => 'sometimes|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'insurance_type' => 'sometimes|in:bpjs,mandiri,asuransi',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'allergies' => 'nullable|string',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'medical_record_number.unique' => 'Nomor rekam medis sudah digunakan.',
            'nik.size' => 'NIK harus 16 digit.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
        ];
    }
}
