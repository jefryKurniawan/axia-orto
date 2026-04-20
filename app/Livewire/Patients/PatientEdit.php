<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Livewire\Component;

class PatientEdit extends Component
{
    public Patient $patient;
    public $name;
    public $medical_record_number;
    public $nik;
    public $date_of_birth;
    public $gender;
    public $blood_type;
    public $insurance_type;
    public $allergies;
    public $phone;
    public $emergency_contact;
    public $address;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'medical_record_number' => 'required|string|unique:patients,medical_record_number,' . $this->patient->id,
            'nik' => 'nullable|string|size:16|unique:patients,nik,' . $this->patient->id,
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'insurance_type' => 'required|in:bpjs,mandiri,asuransi',
            'allergies' => 'nullable|string',
            'phone' => 'nullable|string|max:20',
            'emergency_contact' => 'nullable|string|max:255',
            'address' => 'nullable|string',
        ];
    }

    public function mount(Patient $patient)
    {
        $this->patient = $patient;
        $this->name = $patient->name;
        $this->medical_record_number = $patient->medical_record_number;
        $this->nik = $patient->nik;
        $this->date_of_birth = $patient->date_of_birth ? $patient->date_of_birth->format('Y-m-d') : null;
        $this->gender = $patient->gender;
        $this->blood_type = $patient->blood_type;
        $this->insurance_type = $patient->insurance_type;
        $this->allergies = $patient->allergies;
        $this->phone = $patient->phone;
        $this->emergency_contact = $patient->emergency_contact;
        $this->address = $patient->address;
    }

    public function save()
    {
        $this->validate();

        $this->patient->update([
            'name' => $this->name,
            'medical_record_number' => $this->medical_record_number,
            'nik' => $this->nik,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'blood_type' => $this->blood_type,
            'insurance_type' => $this->insurance_type,
            'allergies' => $this->allergies,
            'phone' => $this->phone,
            'emergency_contact' => $this->emergency_contact,
            'address' => $this->address,
        ]);

        session()->flash('success', 'Data pasien berhasil diperbarui.');
        return redirect()->route('patients.show', $this->patient);
    }

    public function render()
    {
        return view('livewire.patients.patient-edit');
    }
}
