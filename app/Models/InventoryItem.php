<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;

class InventoryItem extends Model
{
    use SoftDeletes, Auditable;
    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'category',
        'unit',
        'quantity',
        'reorder_level',
        'price',
        'is_active',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'reorder_level' => 'integer',
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

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

    public function transactions(): HasMany
    {
        return $this->hasMany(InventoryTransaction::class, 'inventory_item_id');
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->reorder_level;
    }
}
