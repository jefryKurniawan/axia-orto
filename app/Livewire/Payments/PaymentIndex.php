<?php

namespace App\Livewire\Payments;

use App\Models\Payment;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $method = '';
    public $perPage = 10;
    public $selectedRows = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'method' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedMethod()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'method', 'selectedRows']);
        $this->resetPage();
    }

    public function export()
    {
        $count = count($this->selectedRows);

        if ($count === 0) {
            $this->dispatch('swal', [
                'icon' => 'warning',
                'title' => 'Pilih Data',
                'text' => 'Silakan centang transaksi pembayaran yang ingin Anda export terlebih dahulu.',
                'toast' => false
            ]);
            return;
        }

        $selectedIds = $this->selectedRows;

        return response()->streamDownload(function () use ($selectedIds) {
            $handle = fopen('php://output', 'w');
            
            // Document Header
            fputcsv($handle, ['KLINIK AXIA ORTO - PROSTHETIC CLINIC']);
            fputcsv($handle, ['LAPORAN RIWAYAT PEMBAYARAN']);
            fputcsv($handle, ['Tanggal Export:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['Total Data:', count($selectedIds) . ' Transaksi']);
            fputcsv($handle, []); // Empty line spacer

            // Add Table Header
            fputcsv($handle, [
                'No', 
                'Tanggal & Waktu', 
                'No. Transaksi', 
                'Nama Pasien', 
                'No. Order', 
                'Jumlah Bayar',
                'Metode Pembayaran',
                'Kasir'
            ]);

            $payments = Payment::with(['order.patient', 'createdBy'])
                ->whereIn('id', $selectedIds)
                ->latest()
                ->get();
            
            foreach ($payments as $index => $payment) {
                fputcsv($handle, [
                    $index + 1,
                    $payment->created_at->format('d/m/Y H:i'),
                    $payment->payment_number,
                    $payment->order->patient->name ?? '-',
                    $payment->order->order_number ?? '-',
                    $payment->amount_paid,
                    strtoupper($payment->payment_method),
                    $payment->createdBy->name ?? '-'
                ]);
            }

            fclose($handle);
        }, 'export-pembayaran-' . now()->format('Y-m-d-His') . '.csv');
    }

    public function render()
    {
        $payments = Payment::with(['order.patient', 'createdBy'])
            ->when($this->search, function ($query) {
                $query->whereHas('order', function ($q) {
                    $q->where('order_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('patient', function ($pq) {
                          $pq->where('name', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->method, function ($query) {
                $query->where('payment_method', $this->method);
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.payments.payment-index', [
            'payments' => $payments
        ]);
    }
}
