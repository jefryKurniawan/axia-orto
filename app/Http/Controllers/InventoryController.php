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
        $items = InventoryItem::latest()->pagination(10);
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
            'item_code' => 'required|unique:inventory_items',
            'name' => 'required|string|max:255',
            'category' => 'required|in:material,component,tool',
            'unit' => 'required|string|max:20',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        InventoryItem::create($validated);

        return redirect()->route('inventory.index')
            ->with('success', 'Item inventory berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        return view('inventory.show', compact('item'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return view('inventory.edit', compact('item'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $item)
    {
        $validated = $request->validate([
            'item_code' => ['required', Rule::unique('inventory_items')->ignore($item->id)],
            'name' => 'required|string|max:255',
            'category' => 'required|in:material,component,tool',
            'unit' => 'required|string|max:20',
            'current_stock' => 'required|numeric|min:0',
            'min_stock' => 'required|numeric|min:0',
            'cost_price' => 'required|numeric|min:0',
            'is_active' => 'boolean'
        ]);

        $item->update($validated);

        return redirect()->route('inventory.show', $item)
            ->with('success', 'Item inventory berhasil diperbarui.');
    }

    public function toggleStatus(InventoryItem $item)
    {
        $item->update(['is_active' => !$item->is_active]);

        $status = $item->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->back()
            ->with('success', "Item inventory berhasil $status.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
