<?php
// app/Models/Service.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class Service extends Model
{
    use HasFactory, Cacheable;

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'description',
        'service_type',
        'price',
        'duration_days',
        'is_active',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'duration_days' => 'integer',
    ];

    // Relationships
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('service_type', $type);
    }

    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('code', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%");
    }

    // Cache Methods
    public static function getCachedActiveServices()
    {
        return Cache::tags(['Service'])->remember('active_services', 86400, function () {
            return static::active()
                ->orderBy('service_type')
                ->orderBy('name')
                ->get();
        });
    }

    public static function getCachedByType($type)
    {
        return Cache::tags(['Service'])->remember("services.type.{$type}", 86400, function () use ($type) {
            return static::where('service_type', $type)
                ->active()
                ->get();
        });
    }

    public static function getCachedServiceStats()
    {
        return Cache::tags(['Service'])->remember('service_stats', 3600, function () {
            return [
                'total' => static::count(),
                'active' => static::active()->count(),
                'by_type' => static::selectRaw('service_type, count(*) as count')
                    ->groupBy('service_type')
                    ->pluck('count', 'service_type'),
            ];
        });
    }
}
