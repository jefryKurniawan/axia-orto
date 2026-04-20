<?php

namespace App\Livewire;

use Livewire\Component;

class PatientSearch extends Component
{
    public $search = '';
    public $gender = '';
    public $insurance = '';

    public function render()
    {
        $query = Patient::query();

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('medical_record_number', 'like', '%' . $this->search . '%')
                ->orWhere('nik', 'like', '%' . $this->search . '%')
                ->orWhere('phone', 'like', '%' . $this->search . '%');
        }

        if ($this->gender) {
            $query->where('gender', $this->gender);
        }

        if ($this->insurance) {
            $query->where('insurance_type', $this->insurance);
        }

        $patients = $query->latest()->paginate(10);

        return view('livewire.patient-search', [
            'patients' => $patients
        ]);
    }
}
