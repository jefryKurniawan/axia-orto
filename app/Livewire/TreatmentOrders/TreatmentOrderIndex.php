<?php

namespace App\Livewire\TreatmentOrders;

use App\Models\TreatmentOrder;
use Livewire\Component;
use Livewire\WithPagination;

class TreatmentOrderIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $status = '';
    public $perPage = 10;
    public $selectedRows = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedStatus()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'status', 'selectedRows']);
        $this->resetPage();
    }

    public function deleteOrder($id)
    {
        $order = TreatmentOrder::findOrFail($id);
        $order->delete();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Pesanan #' . $order->order_number . ' berhasil dihapus.',
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
                'text' => 'Silakan centang pesanan yang ingin Anda export terlebih dahulu.',
                'toast' => false
            ]);
            return;
        }

        $selectedIds = $this->selectedRows;

        return response()->streamDownload(function () use ($selectedIds) {
            $handle = fopen('php://output', 'w');
            
            // Document Header
            fputcsv($handle, ['KLINIK AXIA ORTO - PROSTHETIC CLINIC']);
            fputcsv($handle, ['LAPORAN PESANAN ALAT']);
            fputcsv($handle, ['Tanggal Export:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['Total Data:', count($selectedIds) . ' Pesanan']);
            fputcsv($handle, []); // Empty line spacer

            // Add Table Header
            fputcsv($handle, [
                'No', 
                'Tanggal Order', 
                'No. Order', 
                'Nama Pasien', 
                'MRN Pasien', 
                'Status'
            ]);

            $orders = \App\Models\TreatmentOrder::with('patient')
                ->whereIn('id', $selectedIds)
                ->latest()
                ->get();
            
            foreach ($orders as $index => $order) {
                fputcsv($handle, [
                    $index + 1,
                    $order->created_at->format('d/m/Y'),
                    $order->order_number,
                    $order->patient->name ?? '-',
                    $order->patient->medical_record_number ?? '-',
                    strtoupper($order->status)
                ]);
            }

            fclose($handle);
        }, 'export-pesanan-' . now()->format('Y-m-d-His') . '.csv');
    }

    public function render()
    {
        $orders = TreatmentOrder::with(['patient', 'consultation'])
            ->when($this->search, function ($query) {
                $query->where('order_number', 'like', '%' . $this->search . '%')
                    ->orWhereHas('patient', function ($q) {
                        $q->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->status, function ($query) {
                $query->where('status', $this->status);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.treatment-orders.treatment-order-index', [
            'orders' => $orders
        ]);
    }
}
