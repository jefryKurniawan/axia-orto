<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use Illuminate\Http\JsonResponse;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Eager load category dan creator untuk menghindari N+1 query problem
        $products = Product::with('category', 'creator')->latest()->get();

        return response()->json([
            'message' => 'Daftar produk berhasil diambil.',
            'data' => $products
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Tambahkan kolom audit
        $data['created_by'] = auth()->id() ?? null;
        $data['uuid'] = (string) \Illuminate\Support\Str::uuid();

        $product = Product::create($data);

        // Load relasi yang baru dibuat sebelum ditampilkan
        $product->load('category', 'creator');

        return response()->json([
            'message' => 'Produk berhasil ditambahkan.',
            'data' => $product
        ], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product): JsonResponse
    {
        // Load relasi untuk detail
        $product->load('category', 'creator');

        return response()->json([
            'message' => 'Detail produk berhasil diambil.',
            'data' => $product
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductRequest $request, Product $product): JsonResponse
    {
        $data = $request->validated();

        // Tambahkan updated_at/updated_by secara eksplisit jika diperlukan
        // $data['updated_by'] = auth()->id() ?? null;

        $product->update($data);

        // Load relasi setelah update
        $product->load('category', 'creator');

        return response()->json([
            'message' => 'Produk berhasil diperbarui.',
            'data' => $product
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Product $product): JsonResponse
    {
        // Peringatan: Sebelum menghapus produk, pastikan tidak ada InventoryItem
        // atau OrderItem yang merujuk padanya, atau implementasikan Soft Deletes.

        $product->delete();

        return response()->json([
            'message' => 'Produk berhasil dihapus.'
        ], 204); // 204 No Content
    }
}
