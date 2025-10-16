<?php

namespace App\Models;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ProductCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'name',
        'description',
        'parent_id',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * Relasi rekursif: Kategori induk (Parent Category).
     */
    public function parent(): BelongsTo
    {
        // Kategori ini belongsTo (milik) kategori lain yang merupakan induknya
        return $this->belongsTo(ProductCategory::class, 'parent_id');
    }

    /**
     * Relasi rekursif: Kategori anak (Child Categories).
     */
    public function children(): HasMany
    {
        // Kategori ini HasMany (memiliki banyak) kategori lain yang merupakan anaknya
        return $this->hasMany(ProductCategory::class, 'parent_id');
    }

    /**
     * Relasi ke User yang membuat kategori.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Relasi ke produk yang menggunakan kategori ini.
     * (Asumsi ada Model Product)
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class, 'category_id'); // Sesuaikan foreign key jika berbeda
    }
}
