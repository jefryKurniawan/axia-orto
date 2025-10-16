<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePatientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            // UUID: tidak perlu divalidasi, dibuat oleh DB atau di Controller
            'medical_record_number' => 'required|string|max:20|unique:patients,medical_record_number',
            'nik' => 'required|string|max:16|unique:patients,nik',
            'name' => 'required|string|max:255',
            'date_of_birth' => 'required|date|before:today',
            'gender' => ['required', Rule::in(['L', 'P'])],
            'phone' => 'nullable|string|max:20',
            'email' => 'nullable|email|max:255|unique:patients,email',
            'address' => 'required|string',
            'province' => 'nullable|string|max:100',
            'city' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:10',
            'emergency_contact_name' => 'nullable|string|max:255',
            'emergency_contact_phone' => 'nullable|string|max:20',
            'insurance_type' => ['nullable', Rule::in(['bpjs', 'mandiri', 'asuransi', 'lainnya'])],
            'insurance_number' => 'nullable|string|max:50',
            'blood_type' => ['nullable', Rule::in(['A', 'B', 'AB', 'O'])],
            'allergies' => 'nullable|string',
            'medical_history' => 'nullable|string',
            'is_active' => 'nullable|boolean',
        ];
    }
}
