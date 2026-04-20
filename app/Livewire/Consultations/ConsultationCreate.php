<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;

class ConsultationCreate extends Component
{
    public $patient_search = '';
    public $patient_id;
    public $doctor_id;
    public $consultation_date;
    public $complaint;
    public $diagnosis;
    public $treatment_plan;
    public $notes;
    public $follow_up_date;
    public $status = 'scheduled';

    protected $rules = [
        'patient_id' => 'required|exists:patients,id',
        'doctor_id' => 'required|exists:users,id',
        'consultation_date' => 'required|date',
        'complaint' => 'required|string',
        'diagnosis' => 'nullable|string',
        'treatment_plan' => 'nullable|string',
        'notes' => 'nullable|string',
        'follow_up_date' => 'nullable|date|after:consultation_date',
        'status' => 'required|in:scheduled,in_progress,completed,cancelled',
    ];

    public function mount()
    {
        $this->consultation_date = now()->format('Y-m-d\TH:i');
    }

    public function selectPatient($id)
    {
        $patient = Patient::find($id);
        $this->patient_id = $id;
        $this->patient_search = $patient->name . ' (' . $patient->medical_record_number . ')';
    }

    public function store()
    {
        $this->validate();

        $consultation = Consultation::create([
            'patient_id' => $this->patient_id,
            'doctor_id' => $this->doctor_id,
            'consultation_date' => $this->consultation_date,
            'complaint' => $this->complaint,
            'diagnosis' => $this->diagnosis,
            'treatment_plan' => $this->treatment_plan,
            'notes' => $this->notes,
            'follow_up_date' => $this->follow_up_date,
            'status' => $this->status,
        ]);

        session()->flash('success', 'Konsultasi berhasil dijadwalkan.');
        return redirect()->route('consultations.index');
    }

    public function render()
    {
        $patients = [];
        if (strlen($this->patient_search) > 2 && !$this->patient_id) {
            $patients = Patient::where('name', 'like', '%' . $this->patient_search . '%')
                ->orWhere('medical_record_number', 'like', '%' . $this->patient_search . '%')
                ->take(5)
                ->get();
        }

        $doctors = User::where('role', 'dokter')->get();

        return view('livewire.consultations.consultation-create', [
            'patients' => $patients,
            'doctors' => $doctors,
        ]);
    }

    public function updatedPatientSearch()
    {
        $this->patient_id = null;
    }
}
