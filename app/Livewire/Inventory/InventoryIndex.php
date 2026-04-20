<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;
use Livewire\WithPagination;

class InventoryIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $perPage = 10;
    public $selectedRows = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'category' => ['except' => ''],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function updatedCategory()
    {
        $this->resetPage();
        $this->selectedRows = [];
    }

    public function resetFilters()
    {
        $this->reset(['search', 'category', 'selectedRows']);
        $this->resetPage();
    }

    public function deleteItem($id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->delete();
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Dihapus',
            'text' => 'Barang ' . $item->name . ' berhasil dihapus.',
            'toast' => true
        ]);
    }

    public function toggleStatus($id)
    {
        $item = InventoryItem::findOrFail($id);
        $item->update(['is_active' => !$item->is_active]);
        $this->dispatch('swal', [
            'icon' => 'success',
            'title' => 'Status Diperbarui',
            'text' => 'Status barang ' . $item->name . ' berhasil diubah.',
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
                'text' => 'Silakan centang item inventori yang ingin Anda export terlebih dahulu.',
                'toast' => false
            ]);
            return;
        }

        $selectedIds = $this->selectedRows;

        return response()->streamDownload(function () use ($selectedIds) {
            $handle = fopen('php://output', 'w');
            
            // Document Header
            fputcsv($handle, ['KLINIK AXIA ORTO - PROSTHETIC CLINIC']);
            fputcsv($handle, ['LAPORAN STOK INVENTORI']);
            fputcsv($handle, ['Tanggal Export:', now()->format('d/m/Y H:i')]);
            fputcsv($handle, ['Total Data:', count($selectedIds) . ' Item']);
            fputcsv($handle, []); // Empty line spacer

            // Add Table Header
            fputcsv($handle, [
                'No', 
                'Kode Barang', 
                'Nama Barang', 
                'Kategori', 
                'Stok Saat Ini', 
                'Satuan',
                'Harga Beli',
                'Status'
            ]);

            $items = \App\Models\InventoryItem::whereIn('id', $selectedIds)->latest()->get();
            
            foreach ($items as $index => $item) {
                fputcsv($handle, [
                    $index + 1,
                    $item->code,
                    $item->name,
                    strtoupper($item->category),
                    $item->quantity,
                    $item->unit,
                    $item->price,
                    $item->is_active ? 'AKTIF' : 'NON-AKTIF'
                ]);
            }

            fclose($handle);
        }, 'export-inventori-' . now()->format('Y-m-d-His') . '.csv');
    }

    public function render()
    {
        $items = InventoryItem::query()
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('code', 'like', '%' . $this->search . '%');
            })
            ->when($this->category, function ($query) {
                $query->where('category', $this->category);
            })
            ->latest()
            ->paginate($this->perPage);

        $stats = [
            'total_items' => InventoryItem::count(),
            'low_stock' => InventoryItem::whereRaw('quantity <= reorder_level')->count(),
            'asset_value' => InventoryItem::sum(\DB::raw('quantity * price')),
        ];

        return view('livewire.inventory.inventory-index', [
            'items' => $items,
            'stats' => $stats
        ]);
    }
}
