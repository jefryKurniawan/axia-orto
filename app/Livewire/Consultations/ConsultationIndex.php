<?php

namespace App\Livewire\Consultations;

use App\Models\Consultation;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class ConsultationIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedStatus = '';
    public $selectedDoctor = '';
    public $selectedRows = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedStatus' => ['except' => ''],
        'selectedDoctor' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedSelectedStatus()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedSelectedDoctor()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'selectedStatus', 'selectedDoctor', 'selectedRows']);
        $this->resetPage();
    }

    public function deleteConsultation($id)
    {
        $consultation = Consultation::findOrFail($id);

        if ($consultation->status === 'completed') {
            $this->dispatch('swal', [
                'icon' => 'error',
                'title' => 'Gagal',
                'text' => 'Tidak dapat menghapus konsultasi yang sudah selesai.',
                'toast' => true
            ]);
            return;
        }

        $consultation->delete();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Jadwal konsultasi berhasil dihapus.',
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
                'text' => 'Silakan centang jadwal konsultasi yang ingin Anda export terlebih dahulu.',
                'toast' => false
            ]);
            return;
        }

        $selectedIds = $this->selectedRows;

        return response()->streamDownload(function () use ($selectedIds) {
            $handle = fopen('php://output', 'w');
            
            // Document Header
            fputcsv($handle, ['KLINIK AXIA ORTO - PROSTHETIC CLINIC']);
            fputcsv($handle, ['LAPORAN JADWAL KONSULTASI']);
            fputcsv($handle, ['Tanggal Export:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['Total Data:', count($selectedIds) . ' Konsultasi']);
            fputcsv($handle, []); // Empty line spacer

            // Add Table Header
            fputcsv($handle, [
                'No', 
                'Waktu & Tanggal', 
                'Nama Pasien', 
                'MRN Pasien', 
                'Dokter', 
                'Keluhan', 
                'Status'
            ]);

            $consultations = \App\Models\Consultation::with(['patient', 'doctor'])
                ->whereIn('id', $selectedIds)
                ->latest()
                ->get();
            
            foreach ($consultations as $index => $consultation) {
                fputcsv($handle, [
                    $index + 1,
                    \Carbon\Carbon::parse($consultation->consultation_date)->format('d/m/Y H:i'),
                    $consultation->patient->name ?? '-',
                    $consultation->patient->medical_record_number ?? '-',
                    $consultation->doctor->name ?? '-',
                    $consultation->complaint,
                    strtoupper($consultation->status)
                ]);
            }

            fclose($handle);
        }, 'export-konsultasi-' . now()->format('Y-m-d-His') . '.csv');
    }

    public function render()
    {
        $consultations = Consultation::with(['patient', 'doctor'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->whereHas('patient', function ($pq) {
                        $pq->where('name', 'like', '%' . $this->search . '%')
                            ->orWhere('medical_record_number', 'like', '%' . $this->search . '%');
                    })
                    ->orWhereHas('doctor', function ($dq) {
                        $dq->where('name', 'like', '%' . $this->search . '%');
                    })
                    ->orWhere('complaint', 'like', '%' . $this->search . '%')
                    ->orWhere('diagnosis', 'like', '%' . $this->search . '%');
                });
            })
            ->when($this->selectedStatus, function ($query) {
                $query->where('status', $this->selectedStatus);
            })
            ->when($this->selectedDoctor, function ($query) {
                $query->where('doctor_id', $this->selectedDoctor);
            })
            ->latest('consultation_date')
            ->paginate($this->perPage);

        $doctors = User::where('role', 'dokter')->get();

        return view('livewire.consultations.consultation-index', [
            'consultations' => $consultations,
            'doctors' => $doctors,
        ]);
    }
}
