<?php

namespace App\Models;

use App\Models\ProductCategory;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\ProductComponent;


class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'category_id',
        'sku',
        'name',
        'description',
        'product_type',
        'unit_price',
        'cost_price',
        'is_taxable',
        'tax_rate',
        'is_active',
        'specifications',
        'manufacturing_time',
        'created_by',
    ];

    public function components(): HasMany
    {
        return $this->hasMany(ProductComponent::class, 'product_id');
    }

    /**
     * Casting tipe data untuk kolom non-standar.
     */
    protected $casts = [
        // Mengkonversi DECIMAL menjadi float atau string sesuai kebutuhan.
        // String lebih aman untuk menghindari masalah presisi floating point.
        'unit_price' => 'decimal:2',
        'cost_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',

        'is_taxable' => 'boolean',
        'is_active' => 'boolean',

        // Mengkonversi kolom JSON menjadi array/object PHP
        'specifications' => 'array',

        'manufacturing_time' => 'integer',
    ];

    /**
     * Produk belongsTo (milik) satu kategori.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(ProductCategory::class, 'category_id');
    }

    /**
     * Produk dibuat oleh satu User.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
