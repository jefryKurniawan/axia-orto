<?php

namespace App\Livewire\Services;

use App\Models\Service;
use Livewire\Component;
use Livewire\WithPagination;

class ServiceIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $perPage = 10;
    public $selectedType = '';
    public $showInactive = false;
    public $selectedRows = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedType' => ['except' => ''],
        'showInactive' => ['except' => false],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedSelectedType()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'selectedType', 'selectedRows']);
        $this->resetPage();
    }

    public function deleteService($id)
    {
        $service = Service::findOrFail($id);
        $service->delete();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Layanan ' . $service->name . ' berhasil dihapus.',
            'toast' => true
        ]);
    }

    public function toggleActive($id)
    {
        $service = Service::findOrFail($id);
        $service->is_active = !$service->is_active;
        $service->save();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Status Diubah',
            'text' => 'Status layanan berhasil diubah.',
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
                'text' => 'Silakan centang layanan yang ingin Anda export terlebih dahulu.',
                'toast' => false
            ]);
            return;
        }

        $selectedIds = $this->selectedRows;

        return response()->streamDownload(function () use ($selectedIds) {
            $handle = fopen('php://output', 'w');
            
            // Document Header
            fputcsv($handle, ['KLINIK AXIA ORTO - PROSTHETIC CLINIC']);
            fputcsv($handle, ['LAPORAN KATALOG LAYANAN']);
            fputcsv($handle, ['Tanggal Export:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['Total Data:', count($selectedIds) . ' Layanan']);
            fputcsv($handle, []); // Empty line spacer

            // Add Table Header
            fputcsv($handle, [
                'No', 
                'Kode Layanan', 
                'Nama Layanan', 
                'Kategori/Tipe', 
                'Harga Dasar',
                'Status'
            ]);

            $services = \App\Models\Service::whereIn('id', $selectedIds)->latest()->get();
            
            foreach ($services as $index => $service) {
                fputcsv($handle, [
                    $index + 1,
                    $service->code,
                    $service->name,
                    strtoupper($service->type),
                    $service->base_price,
                    $service->is_active ? 'AKTIF' : 'NON-AKTIF'
                ]);
            }

            fclose($handle);
        }, 'export-layanan-' . now()->format('Y-m-d-His') . '.csv');
    }

    public function render()
    {
        $services = Service::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            })
            ->when($this->selectedType, function ($query) {
                $query->where('service_type', $this->selectedType);
            })
            ->when(!$this->showInactive, function ($query) {
                $query->where('is_active', true);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.services.service-index', [
            'services' => $services,
            'types' => $this->getServiceTypes(),
        ]);
    }

    private function getServiceTypes()
    {
        return [
            'konsultasi' => 'Konsultasi',
            'ortosis' => 'Ortosis',
            'protesis' => 'Protesis',
            'terapi' => 'Terapi',
            'alat' => 'Alat Bantu',
        ];
    }
}
