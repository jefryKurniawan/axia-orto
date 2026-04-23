<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use App\Models\Patient;
use App\Models\User;
use Livewire\Component;

class ConsultationEdit extends Component
{
    public Consultation $consultation;
    public $patient_id;
    public $doctor_id;
    public $consultation_date;
    public $complaint;
    public $diagnosis;
    public $treatment_plan;
    public $notes;
    public $follow_up_date;
    public $status;
    public $patients;
    public $doctors;

    public function mount(Consultation $consultation)
    {
        $this->consultation = $consultation;
        $this->patient_id = $consultation->patient_id;
        $this->doctor_id = $consultation->doctor_id;
        $this->consultation_date = $consultation->consultation_date->format('Y-m-d\TH:i');
        $this->complaint = $consultation->complaint;
        $this->diagnosis = $consultation->diagnosis;
        $this->treatment_plan = $consultation->treatment_plan;
        $this->notes = $consultation->notes;
        $this->follow_up_date = $consultation->follow_up_date?->format('Y-m-d');
        $this->status = $consultation->status;
        $this->patients = Patient::all();
        $this->doctors = User::where('role', 'dokter')->where('is_active', true)->get();
    }

    public function save()
    {
        $this->validate([
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'required|exists:users,id',
            'consultation_date' => 'required|date',
            'complaint' => 'required|string',
            'diagnosis' => 'required|string',
            'status' => 'required|in:scheduled,in_progress,completed,cancelled'
        ]);

        $this->consultation->update([
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

        session()->flash('success', 'Konsultasi berhasil diperbarui.');
        return redirect()->route('consultations.show', $this->consultation);
    }

    public function render()
    {
        return view('livewire.consultations.consultation-edit');
    }
}