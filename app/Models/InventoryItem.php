<?php
// app/Models/InventoryItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class InventoryItem extends Model
{
    use HasFactory, Cacheable;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'category',
        'unit',
        'quantity',
        'reorder_level',
        'price',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'decimal:2',
        'reorder_level' => 'decimal:2',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeLowStock($query)
    {
        return $query->whereRaw('quantity <= reorder_level')
            ->where('is_active', true);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%");
    }

    // Cache Methods
    public static function getCachedLowStockItems()
    {
        return Cache::tags(['InventoryItem'])->remember('low_stock_items', 1800, function () {
            return static::lowStock()->get();
        });
    }

    public static function getCachedByCategory($category)
    {
        return Cache::tags(['InventoryItem'])->remember("inventory.category.{$category}", 3600, function () use ($category) {
            return static::where('category', $category)
                ->active()
                ->get();
        });
    }

    public static function getCachedInventoryStats()
    {
        return Cache::tags(['InventoryItem'])->remember('inventory_stats', 3600, function () {
            return [
                'total_items' => static::count(),
                'active_items' => static::active()->count(),
                'low_stock_count' => static::lowStock()->count(),
                'total_inventory_value' => static::active()->sum(\DB::raw('quantity * price')),
                'by_category' => static::selectRaw('category, count(*) as count')
                    ->groupBy('category')
                    ->pluck('count', 'category'),
            ];
        });
    }

    // Business Logic
    public function isLowStock()
    {
        return $this->quantity <= $this->reorder_level;
    }

    public function updateStock($qty)
    {
        $this->quantity += $qty;
        return $this->save();
    }
}
