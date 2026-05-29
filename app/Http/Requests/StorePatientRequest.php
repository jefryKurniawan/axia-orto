<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
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
            'medical_record_number' => 'nullable|string|max:20|unique:patients',
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|size:16',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'insurance_type' => 'required|in:bpjs,mandiri,asuransi',
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
            'medical_record_number.required' => 'Nomor rekam medis harus diisi.',
            'medical_record_number.unique' => 'Nomor rekam medis sudah digunakan.',
            'name.required' => 'Nama pasien harus diisi.',
            'nik.size' => 'NIK harus 16 digit.',
            'date_of_birth.required' => 'Tanggal lahir harus diisi.',
            'date_of_birth.before' => 'Tanggal lahir harus sebelum hari ini.',
            'gender.required' => 'Jenis kelamin harus dipilih.',
        ];
    }
}
