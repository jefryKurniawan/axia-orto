<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;

class Service extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'code',
        'name',
        'description',
        'service_type',
        'price',
        'duration_days',
        'is_active',
        'uuid'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($service) {
            $service->uuid = (string) \Illuminate\Support\Str::uuid();
            if (!$service->code) {
                $service->code = self::generateServiceCode($service->service_type);
            }
        });
    }

    private static function generateServiceCode($type)
    {
        $prefix = match ($type) {
            'konsultasi' => 'KONS',
            'ortosis' => 'ORT',
            'protesis' => 'PROT',
            'terapi' => 'TER',
            'alat' => 'ALT',
            default => 'SRV',
        };

        $count = self::where('service_type', $type)->count() + 1;
        return "{$prefix}-" . str_pad($count, 3, '0', STR_PAD_LEFT);
    }

    public function getTypeBadgeAttribute()
    {
        return match ($this->service_type) {
            'konsultasi' => '<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Konsultasi</span>',
            'ortosis' => '<span class="bg-purple-100 text-purple-800 text-xs font-medium px-2.5 py-0.5 rounded">Ortosis</span>',
            'protesis' => '<span class="bg-pink-100 text-pink-800 text-xs font-medium px-2.5 py-0.5 rounded">Protesis</span>',
            'terapi' => '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Terapi</span>',
            'alat' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Alat Bantu</span>',
            default => '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Unknown</span>',
        };
    }

    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->price, 0, ',', '.');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
