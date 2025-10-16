<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ProductCategory;
use Illuminate\Http\Request;
use App\Http\Requests\StoreProductCategoryRequest;
use App\Http\Requests\UpdateProductCategoryRequest;
use Illuminate\Http\JsonResponse;

class ProductCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): JsonResponse
    {
        // Ambil kategori level atas (parent_id is null) dan load relasi anak.
        // Anda bisa memilih untuk mengambil semua atau hanya level 1.
        $categories = ProductCategory::with('children', 'parent')
            ->whereNull('parent_id') // Filter hanya kategori level 1
            ->get();

        return response()->json([
            'message' => 'Daftar kategori produk berhasil diambil.',
            'data' => $categories
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreProductCategoryRequest $request): JsonResponse
    {
        $data = $request->validated();

        // Optional: Tambahkan kolom audit
        $data['created_by'] = auth()->id() ?? 1; // Ganti 1 dengan ID user default jika tidak login
        $data['uuid'] = (string) \Illuminate\Support\Str::uuid();

        $category = ProductCategory::create($data);

        return response()->json([
            'message' => 'Kategori produk berhasil ditambahkan.',
            'data' => $category
        ], 201); // 201 Created
    }

    /**
     * Display the specified resource.
     */
    public function show(ProductCategory $productCategory): JsonResponse
    {
        // Load relasi parent dan children saat menampilkan detail
        $productCategory->load('parent', 'children');

        return response()->json([
            'message' => 'Detail kategori produk berhasil diambil.',
            'data' => $productCategory
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateProductCategoryRequest $request, ProductCategory $productCategory): JsonResponse
    {
        $productCategory->update($request->validated());

        return response()->json([
            'message' => 'Kategori produk berhasil diperbarui.',
            'data' => $productCategory
        ]);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProductCategory $productCategory): JsonResponse
    {
        // Peringatan: Hapus kategori mungkin perlu logika tambahan
        // (misalnya: memindahkan anak-anak ke kategori lain atau menghapusnya).

        // Jika Anda ingin menghapus semua anak ketika parent dihapus:
        // $productCategory->children()->delete();

        $productCategory->delete();

        return response()->json([
            'message' => 'Kategori produk berhasil dihapus.'
        ], 204); // 204 No Content
    }
}
