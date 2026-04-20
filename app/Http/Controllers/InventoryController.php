<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Validation\Rule;

class InventoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $items = InventoryItem::latest()->paginate(10);
        return view('inventory.index', compact('items'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('inventory.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|unique:inventory_items',
            'name' => 'required|string|max:255',
            'category' => 'required|in:bahan_baku,komponen,alat_jadi',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $validated['uuid'] = (string) \Illuminate\Support\Str::uuid();
        InventoryItem::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item inventory berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryItem $inventory)
    {
        return view('inventory.show', ['item' => $inventory]);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(InventoryItem $inventory)
    {
        return view('inventory.edit', ['item' => $inventory]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $inventory)
    {
        $validated = $request->validate([
            'code' => ['required', Rule::unique('inventory_items')->ignore($inventory->id)],
            'name' => 'required|string|max:255',
            'category' => 'required|in:bahan_baku,komponen,alat_jadi',
            'unit' => 'required|string|max:20',
            'quantity' => 'required|numeric|min:0',
            'reorder_level' => 'required|numeric|min:0',
            'price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $inventory->update($validated);

        return redirect()->route('inventory.show', $inventory)
            ->with('success', 'Item inventory berhasil diperbarui.');
    }

    public function toggleStatus(InventoryItem $inventory)
    {
        $inventory->update(['is_active' => !$inventory->is_active]);

        $status = $inventory->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Item inventory berhasil $status.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventory)
    {
        $inventory->delete();
        return redirect()->route('inventory.index')->with('success', 'Item berhasil dihapus');
    }
}
