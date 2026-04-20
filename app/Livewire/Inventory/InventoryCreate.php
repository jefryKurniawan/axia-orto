<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;

class InventoryCreate extends Component
{
    public $name;
    public $item_code;
    public $category = 'material';
    public $unit = 'pcs';
    public $min_stock = 0;
    public $current_stock = 0;
    public $cost_price = 0;
    public $selling_price = 0;
    public $description;

    protected $rules = [
        'name' => 'required|string|max:255',
        'item_code' => 'required|string|unique:inventory_items,item_code',
        'category' => 'required|in:material,component,tool',
        'unit' => 'required|string|max:20',
        'min_stock' => 'required|numeric|min:0',
        'current_stock' => 'required|numeric|min:0',
        'cost_price' => 'required|numeric|min:0',
        'selling_price' => 'required|numeric|min:0',
        'description' => 'nullable|string',
    ];

    public function mount()
    {
        $this->item_code = 'ITEM-' . strtoupper(bin2hex(random_bytes(3)));
    }

    public function save()
    {
        $this->validate();

        InventoryItem::create([
            'name' => $this->name,
            'item_code' => $this->item_code,
            'category' => $this->category,
            'unit' => $this->unit,
            'min_stock' => $this->min_stock,
            'current_stock' => $this->current_stock,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'description' => $this->description,
            'is_active' => true,
        ]);

        session()->flash('success', 'Barang berhasil ditambahkan.');
        return redirect()->route('inventory.index');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-create');
    }
}
