<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;

class InventoryEdit extends Component
{
    public InventoryItem $item;
    public $name;
    public $item_code;
    public $category;
    public $unit;
    public $min_stock;
    public $current_stock;
    public $cost_price;
    public $selling_price;
    public $description;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'item_code' => 'required|string|unique:inventory_items,item_code,' . $this->item->id,
            'category' => 'required|in:material,component,tool',
            'unit' => 'required|string|max:20',
            'min_stock' => 'required|numeric|min:0',
            'current_stock' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'selling_price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
        ];
    }

    public function mount(InventoryItem $item)
    {
        $this->item = $item;
        $this->name = $item->name;
        $this->item_code = $item->item_code;
        $this->category = $item->category;
        $this->unit = $item->unit;
        $this->min_stock = $item->min_stock;
        $this->current_stock = $item->current_stock;
        $this->cost_price = $item->cost_price;
        $this->selling_price = $item->selling_price;
        $this->description = $item->description;
    }

    public function save()
    {
        $this->validate();

        $this->item->update([
            'name' => $this->name,
            'item_code' => $this->item_code,
            'category' => $this->category,
            'unit' => $this->unit,
            'min_stock' => $this->min_stock,
            'current_stock' => $this->current_stock,
            'cost_price' => $this->cost_price,
            'selling_price' => $this->selling_price,
            'description' => $this->description,
        ]);

        session()->flash('success', 'Barang berhasil diperbarui.');
        return redirect()->route('inventory.index');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-edit');
    }
}
