<?php

namespace App\Livewire\Inventory;

use App\Models\InventoryItem;
use Livewire\Component;

class InventoryEdit extends Component
{
    public InventoryItem $item;
    public $name;
    public $code;
    public $category;
    public $unit;
    public $quantity;
    public $reorder_level;
    public $price;
    public $description;
    public $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'code' => 'required|string|unique:inventory_items,code,' . $this->item->id,
            'category' => 'required|string|max:50',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    public function mount(InventoryItem $item)
    {
        $this->item = $item;
        $this->name = $item->name;
        $this->code = $item->code;
        $this->category = $item->category;
        $this->unit = $item->unit;
        $this->quantity = $item->quantity;
        $this->reorder_level = $item->reorder_level;
        $this->price = $item->price;
        $this->description = $item->description;
        $this->is_active = $item->is_active;
    }

    public function save()
    {
        $this->validate();

        $this->item->update([
            'name' => $this->name,
            'code' => $this->code,
            'category' => $this->category,
            'unit' => $this->unit,
            'quantity' => $this->quantity,
            'reorder_level' => $this->reorder_level,
            'price' => $this->price,
            'description' => $this->description,
            'is_active' => $this->is_active,
        ]);

        session()->flash('success', 'Item inventori berhasil diperbarui.');
        return redirect()->route('inventory.index');
    }

    public function render()
    {
        return view('livewire.inventory.inventory-edit');
    }
}