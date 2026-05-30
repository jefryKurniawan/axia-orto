<?php

namespace App\Http\Controllers\Api;

use App\Helpers\CacheHelper;
use App\Http\Controllers\Controller;
use App\Http\Requests\AdjustStockRequest;
use App\Http\Requests\StoreInventoryItemRequest;
use App\Http\Requests\UpdateInventoryItemRequest;
use App\Models\InventoryItem;
use App\Models\InventoryTransaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class InventoryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $search = $request->input('search') ?? '';
        $category = $request->input('category') ?? '';
        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $cacheKey = CacheHelper::key('inventory', 'list', [
            'page' => $page,
            'search' => $search,
            'category' => $category,
        ]);

        $result = CacheHelper::remember($cacheKey, 300, function () use ($search, $category, $perPage) {
            $query = DB::table('inventory_items')
                ->select(
                    'id', 'uuid', 'code', 'name', 'category', 'unit',
                    'quantity', 'reorder_level', 'price', 'is_active', 'created_at'
                );

            if ($search !== '') {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('code', 'like', "%{$search}%");
                });
            }

            if ($category !== '') {
                $query->where('category', $category);
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        });

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }

    public function show(string $uuid): JsonResponse
    {
        $item = InventoryItem::where('uuid', $uuid)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item inventory tidak ditemukan.'], 404);
        }

        $recentTransactions = InventoryTransaction::where('inventory_item_id', $item->id)
            ->leftJoin('users', 'inventory_transactions.created_by', '=', 'users.id')
            ->select('inventory_transactions.*', 'users.name as created_by_name')
            ->latest()
            ->limit(10)
            ->get();

        return response()->json([
            'success' => true,
            'data' => [
                'item' => $item,
                'recent_transactions' => $recentTransactions,
            ],
        ]);
    }

    public function store(StoreInventoryItemRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $item = InventoryItem::create([
            'uuid' => Str::uuid(),
            'code' => $validated['code'],
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'category' => $validated['category'],
            'unit' => $validated['unit'],
            'quantity' => $validated['quantity'],
            'reorder_level' => $validated['reorder_level'],
            'price' => $validated['price'],
            'is_active' => $validated['is_active'] ?? true,
        ]);

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Item inventory berhasil ditambahkan.',
        ], 201);
    }

    public function update(UpdateInventoryItemRequest $request, string $uuid): JsonResponse
    {
        $item = InventoryItem::where('uuid', $uuid)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item inventory tidak ditemukan.'], 404);
        }

        $validated = $request->validated();
        $item->update($validated);

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Item inventory berhasil diperbarui.',
        ]);
    }

    public function destroy(string $uuid): JsonResponse
    {
        $item = InventoryItem::where('uuid', $uuid)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item inventory tidak ditemukan.'], 404);
        }

        $hasTransactions = InventoryTransaction::where('inventory_item_id', $item->id)->exists();
        if ($hasTransactions) {
            return response()->json(['success' => false, 'message' => 'Item dengan riwayat transaksi tidak bisa dihapus.'], 422);
        }

        $item->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item inventory berhasil dihapus.',
        ]);
    }

    public function stats(): JsonResponse
    {
        $cacheKey = CacheHelper::key('inventory', 'stats');

        $stats = CacheHelper::remember($cacheKey, 300, function () {
            return [
                'total_items' => (int) DB::table('inventory_items')->count(),
                'active_items' => (int) DB::table('inventory_items')->where('is_active', true)->count(),
                'low_stock_count' => (int) DB::table('inventory_items')
                    ->whereRaw('quantity <= reorder_level')
                    ->where('is_active', true)
                    ->count(),
                'total_inventory_value' => (float) DB::table('inventory_items')
                    ->where('is_active', true)
                    ->selectRaw('COALESCE(SUM(quantity * price), 0) as total')
                    ->value('total'),
                'by_category' => DB::table('inventory_items')
                    ->select('category', DB::raw('count(*) as count'))
                    ->where('is_active', true)
                    ->groupBy('category')
                    ->pluck('count', 'category')
                    ->mapWithKeys(fn ($v, $k) => [$k => (int) $v]),
            ];
        });

        return response()->json(['success' => true, 'data' => $stats]);
    }

    public function lowStock(): JsonResponse
    {
        $cacheKey = CacheHelper::key('inventory', 'low_stock');

        $items = CacheHelper::remember($cacheKey, 300, function () {
            return DB::table('inventory_items')
                ->select('id', 'uuid', 'code', 'name', 'category', 'quantity', 'reorder_level', 'unit')
                ->whereRaw('quantity <= reorder_level')
                ->where('is_active', true)
                ->orderBy('quantity')
                ->get();
        });

        return response()->json(['success' => true, 'data' => $items]);
    }

    public function adjustStock(AdjustStockRequest $request, string $uuid): JsonResponse
    {
        $item = InventoryItem::where('uuid', $uuid)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item inventory tidak ditemukan.'], 404);
        }

        $validated = $request->validated();
        $type = $validated['type'];
        $qty = $validated['quantity'];

        // Calculate actual quantity change
        if ($type === 'keluar') {
            $change = -$qty;
        } else {
            $change = $qty; // masuk or adjustment positive
        }

        $newQuantity = $item->quantity + $change;
        if ($newQuantity < 0) {
            return response()->json(['success' => false, 'message' => 'Stok tidak cukup. Stok saat ini: ' . $item->quantity], 422);
        }

        DB::transaction(function () use ($item, $type, $qty, $change, $validated) {
            $item->update(['quantity' => $newQuantity]);

            InventoryTransaction::create([
                'uuid' => Str::uuid(),
                'inventory_item_id' => $item->id,
                'type' => $type,
                'quantity' => $change,
                'reference_type' => 'manual',
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);
        });

        $item->refresh();

        return response()->json([
            'success' => true,
            'data' => $item,
            'message' => 'Stok berhasil diperbarui.',
        ]);
    }

    public function transactions(Request $request, string $uuid): JsonResponse
    {
        $item = InventoryItem::where('uuid', $uuid)->first();

        if (!$item) {
            return response()->json(['success' => false, 'message' => 'Item inventory tidak ditemukan.'], 404);
        }

        $page = (int) $request->input('page', 1);
        $perPage = 15;

        $result = DB::table('inventory_transactions')
            ->leftJoin('users', 'inventory_transactions.created_by', '=', 'users.id')
            ->where('inventory_transactions.inventory_item_id', $item->id)
            ->select(
                'inventory_transactions.id',
                'inventory_transactions.uuid',
                'inventory_transactions.type',
                'inventory_transactions.quantity',
                'inventory_transactions.notes',
                'inventory_transactions.created_at',
                'users.name as created_by_name'
            )
            ->orderBy('inventory_transactions.created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $result->items(),
            'meta' => [
                'current_page' => $result->currentPage(),
                'last_page' => $result->lastPage(),
                'per_page' => $result->perPage(),
                'total' => $result->total(),
            ],
        ]);
    }
}
