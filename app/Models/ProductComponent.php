<?php

namespace App\Models;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductComponent extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid',
        'product_id',
        'component_name',
        'material_type',
        'quantity',
        'unit',
        'unit_cost',
        'total_cost', // Ini harus dihitung, tapi tetap di-fillable
        'specifications',
    ];

    protected $casts = [
        // Menggunakan decimal:2 untuk menjaga presisi dua angka di belakang koma
        'quantity' => 'decimal:2',
        'unit_cost' => 'decimal:2',
        'total_cost' => 'decimal:2',

        // Mengkonversi kolom JSON menjadi array/object PHP
        'specifications' => 'array',
    ];

    /**
     * Komponen belongsTo (milik) satu produk.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    /**
     * Mutator untuk memastikan total_cost dihitung otomatis
     * sebelum disimpan ke database.
     */
    public function setTotalCostAttribute($value = null): void
    {
        // Jika kolom total_cost tidak diisi, hitung dari quantity * unit_cost
        $quantity = $this->quantity;
        $unitCost = $this->unit_cost;

        // Pastikan quantity dan unit_cost sudah tersedia saat dipanggil
        if ($quantity !== null && $unitCost !== null) {
            $this->attributes['total_cost'] = $quantity * $unitCost;
        } else {
            // Jika Anda mengirim total_cost secara manual, gunakan nilai itu.
            $this->attributes['total_cost'] = $value;
        }
    }
}
