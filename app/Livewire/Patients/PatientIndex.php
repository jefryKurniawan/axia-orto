<?php

namespace App\Livewire\Patients;

use App\Models\Patient;
use Livewire\Component;
use Livewire\WithPagination;
use Symfony\Component\HttpFoundation\StreamedResponse;

class PatientIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $gender = '';
    public $insurance = '';
    public $status = '';
    public $selectedRows = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'gender' => ['except' => ''],
        'insurance' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedGender()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedInsurance()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'gender', 'insurance', 'selectedRows']);
        $this->resetPage();
    }

    public function deletePatient($id)
    {
        $patient = Patient::findOrFail($id);
        $patient->delete();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Data pasien ' . $patient->name . ' berhasil dihapus.',
            'toast' => true
        ]);
    }

    public function export()
    {
        $count = count($this->selectedRows);

        if ($count === 0) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Pilih Data',
                'text' => 'Silakan centang data pasien yang ingin Anda export terlebih dahulu.',
                'toast' => false
            ]);
            return;
        }

        $selectedIds = $this->selectedRows;
        
        return response()->streamDownload(function () use ($selectedIds) {
            $handle = fopen('php://output', 'w');
            
            // Document Header
            fputcsv($handle, ['KLINIK AXIA ORTO - PROSTHETIC CLINIC']);
            fputcsv($handle, ['LAPORAN DATA PASIEN']);
            fputcsv($handle, ['Tanggal Export:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['Total Data:', count($selectedIds) . ' Pasien']);
            fputcsv($handle, []); // Empty line spacer

            // Add Table Header
            fputcsv($handle, [
                'No', 
                'No. MRN', 
                'Nama Lengkap', 
                'NIK', 
                'Jenis Kelamin', 
                'Tanggal Lahir', 
                'Telepon', 
                'Asuransi',
                'Alamat'
            ]);

            $patients = Patient::whereIn('id', $selectedIds)->latest()->get();
            
            foreach ($patients as $index => $patient) {
                fputcsv($handle, [
                    $index + 1,
                    $patient->medical_record_number,
                    $patient->name,
                    $patient->nik,
                    $patient->gender == 'L' ? 'Laki-laki' : 'Perempuan',
                    $patient->date_of_birth,
                    $patient->phone,
                    $patient->insurance_provider ?? 'Umum',
                    $patient->address
                ]);
            }

            fclose($handle);
        }, 'export-pasien-' . now()->format('Y-m-d-His') . '.csv');
    }

    public function render()
    {
        $patients = Patient::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('medical_record_number', 'like', '%' . $this->search . '%')
                      ->orWhere('nik', 'like', '%' . $this->search . '%')
                      ->orWhere('phone', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->gender, function ($query) {
                $query->where('gender', $this->gender);
            })
            ->when($this->insurance, function ($query) {
                $query->where('insurance_type', $this->insurance);
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate(10);

        return view('livewire.patients.patient-index', [
            'patients' => $patients
        ]);
    }
}
