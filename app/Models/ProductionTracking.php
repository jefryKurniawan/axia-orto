<?php
// app/Models/ProductionTracking.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\DB;

class ProductionTracking extends Model
{
    use HasFactory, Auditable;

    protected $table = 'production_trackings';

    protected $fillable = [
        'uuid',
        'treatment_order_id',
        'step',
        'status',
        'notes',
        'assigned_to',
        'completed_by',
        'started_at',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(TreatmentOrder::class, 'treatment_order_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function completedBy()
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    // Scopes
    public function scopeCurrentStage($query, $orderId)
    {
        return $query->where('treatment_order_id', $orderId)
            ->whereNull('completed_at')
            ->latest();
    }

    public function scopeCompletedStages($query, $orderId)
    {
        return $query->where('treatment_order_id', $orderId)
            ->whereNotNull('completed_at')
            ->orderBy('completed_at');
    }

    public function scopeByStep($query, $step)
    {
        return $query->where('step', $step);
    }

    // Cache Methods
    public static function getCachedOrderProgress($orderId)
    {
        $key = CacheHelper::key('production', 'order_progress', ['order' => $orderId]);
        return CacheHelper::remember($key, 1800, function () use ($orderId) {
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
