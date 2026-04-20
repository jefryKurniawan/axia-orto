<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Livewire\Component;

class PatientCreate extends Component
{
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

    protected $rules = [
        'name' => 'required|string|max:255',
        'medical_record_number' => 'required|string|unique:patients,medical_record_number',
        'nik' => 'nullable|string|size:16|unique:patients,nik',
        'date_of_birth' => 'required|date|before:today',
        'gender' => 'required|in:L,P',
        'blood_type' => 'nullable|in:A,B,AB,O',
        'insurance_type' => 'required|in:bpjs,mandiri,asuransi',
        'allergies' => 'nullable|string',
        'phone' => 'nullable|string|max:20',
        'emergency_contact' => 'nullable|string|max:255',
        'address' => 'nullable|string',
    ];

    public function mount()
    {
        // Auto-generate MRN if empty
        $this->medical_record_number = 'MRN-' . date('Ymd') . '-' . str_pad(Patient::count() + 1, 3, '0', STR_PAD_LEFT);
    }

    public function save()
    {
        $this->validate();

        Patient::create([
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
            'status' => 'active',
        ]);

        session()->flash('success', 'Pasien berhasil ditambahkan.');
        return redirect()->route('patients.index');
    }

    public function render()
    {
        return view('livewire.patients.patient-create');
    }
}
