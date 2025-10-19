<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\InventoryItem;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;

class InventoryItemController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Menggunakan with() untuk memuat relasi (eager loading) jika diperlukan
        $items = InventoryItem::with(['warehouse', 'supplier', 'creator'])->get();

        return response()->json([
            'status' => 'success',
            'message' => 'Daftar item inventaris berhasil diambil',
            'data' => $items
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'required|integer|exists:warehouses,id',
            'item_code' => 'required|string|max:50|unique:inventory_items,item_code',
            'item_name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|string|max:100',
            'unit' => 'required|string|max:20',
            'current_stock' => 'nullable|numeric|min:0',
            'min_stock' => 'nullable|numeric|min:0',
            'max_stock' => 'nullable|numeric|min:0|gte:min_stock', // max_stock harus lebih besar/sama dengan min_stock
            'unit_cost' => 'required|numeric|min:0',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'is_active' => 'boolean',
            'created_by' => 'nullable|integer|exists:users,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirim tidak valid',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // Status 422
        }

        $validatedData = $validator->validated();

        // 2. Hitung total_value sebelum menyimpan
        $validatedData['total_value'] = ($validatedData['current_stock'] ?? 0) * $validatedData['unit_cost'];

        // 3. Buat dan simpan InventoryItem baru
        try {
            $item = InventoryItem::create($validatedData);

            return response()->json([
                'status' => 'success',
                'message' => 'Item inventaris berhasil ditambahkan',
                'data' => $item
            ], Response::HTTP_CREATED); // Status 201
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menyimpan item inventaris: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // Status 500
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(InventoryItem $inventoryItem)
    {
        // Memuat relasi untuk tampilan detail
        $inventoryItem->load(['warehouse', 'supplier', 'creator']);

        return response()->json([
            'status' => 'success',
            'message' => 'Detail item inventaris berhasil diambil',
            'data' => $inventoryItem
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, InventoryItem $inventoryItem)
    {
        // 1. Validasi input
        $validator = Validator::make($request->all(), [
            'warehouse_id' => 'sometimes|integer|exists:warehouses,id',
            // Pastikan item_code unik, kecuali untuk item yang sedang diedit
            'item_code' => 'sometimes|string|max:50|unique:inventory_items,item_code,' . $inventoryItem->id,
            'item_name' => 'sometimes|string|max:255',
            'description' => 'nullable|string',
            'category' => 'sometimes|string|max:100',
            'unit' => 'sometimes|string|max:20',
            'current_stock' => 'sometimes|numeric|min:0',
            'min_stock' => 'sometimes|numeric|min:0',
            'max_stock' => 'sometimes|numeric|min:0|gte:min_stock',
            'unit_cost' => 'sometimes|numeric|min:0',
            'supplier_id' => 'nullable|integer|exists:suppliers,id',
            'is_active' => 'sometimes|boolean',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang dikirim tidak valid',
                'errors' => $validator->errors()
            ], Response::HTTP_UNPROCESSABLE_ENTITY); // Status 422
        }

        $validatedData = $validator->validated();

        // 2. Hitung ulang total_value jika stok atau biaya satuan berubah
        $currentStock = $validatedData['current_stock'] ?? $inventoryItem->current_stock;
        $unitCost = $validatedData['unit_cost'] ?? $inventoryItem->unit_cost;
        $validatedData['total_value'] = $currentStock * $unitCost;

        // 3. Perbarui data
        try {
            $inventoryItem->update($validatedData);

            // Muat ulang relasi jika ada perubahan
            $inventoryItem->load(['warehouse', 'supplier', 'creator']);

            return response()->json([
                'status' => 'success',
                'message' => 'Item inventaris berhasil diperbarui',
                'data' => $inventoryItem
            ], Response::HTTP_OK); // Status 200
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal memperbarui item inventaris: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // Status 500
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(InventoryItem $inventoryItem)
    {
        try {
            $inventoryItem->delete();

            return response()->json([
                'status' => 'success',
                'message' => 'Item inventaris berhasil dihapus'
            ], Response::HTTP_NO_CONTENT); // Status 204
        } catch (\Exception $e) {
            return response()->json([
                'status' => 'error',
                'message' => 'Gagal menghapus item inventaris: ' . $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR); // Status 500
        }
    }
}
