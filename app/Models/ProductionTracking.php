<?php
// app/Models/ProductionTracking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class ProductionTracking extends Model
{
    use HasFactory, Cacheable;

    protected $table = 'production_tracking';

    protected $fillable = [
        'order_id',
        'production_stage',
        'notes',
        'completed_by',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(TreatmentOrder::class);
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Scopes
    public function scopeCurrentStage($query, $orderId)
    {
        return $query->where('order_id', $orderId)
            ->whereNull('completed_at')
            ->latest();
    }

    public function scopeCompletedStages($query, $orderId)
    {
        return $query->where('order_id', $orderId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at');
    }

    public function scopeByStage($query, $stage)
    {
        return $query->where('production_stage', $stage);
    }

    // Cache Methods
    public static function getCachedOrderProgress($orderId)
    {
        return Cache::tags(['ProductionTracking'])->remember("order_progress.{$orderId}", 1800, function () use ($orderId) {
            return [
                'completed' => static::completedStages($orderId)->get(),
                'current' => static::currentStage($orderId)->first(),
            ];
        });
    }

    // Business Logic
    public function complete($completedBy, $notes = null)
    {
        $this->completed_by = $completedBy;
        $this->completed_at = now();
        $this->notes = $notes ?? $this->notes;
        return $this->save();
    }

    public function isCompleted()
    {
        return !is_null($this->completed_at);
    }
}
