<?php
// app/Models/TreatmentOrder.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class TreatmentOrder extends Model
{
    use HasFactory, Cacheable;

    protected $fillable = [
        'uuid',
        'order_number',
        'patient_id',
        'consultation_id',
        'order_date',
        'delivery_date',
        'total_amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'order_date' => 'date',
        'delivery_date' => 'date',
        'total_amount' => 'decimal:2',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function consultation()
    {
        return $this->belongsTo(Consultation::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function productionTrackings()
    {
        return $this->hasMany(ProductionTracking::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    // Scopes
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('order_date', '>=', now()->subDays($days));
    }

    public function scopePending($query)
    {
        return $query->whereIn('status', ['draft', 'confirmed', 'production']);
    }

    public function scopeCompleted($query)
    {
        return $query->whereIn('status', ['ready', 'delivered']);
    }

    // Cache Methods
    public static function getCachedRecentOrders($days = 30)
    {
        return Cache::tags(['TreatmentOrder'])->remember("recent_orders.{$days}", 1800, function () use ($days) {
            return static::recent($days)
                ->with(['patient', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public static function getCachedOrderStats()
    {
        return Cache::tags(['TreatmentOrder'])->remember('order_stats', 3600, function () {
            return [
                'total' => static::count(),
                'draft' => static::where('status', 'draft')->count(),
                'confirmed' => static::where('status', 'confirmed')->count(),
                'production' => static::where('status', 'production')->count(),
                'ready' => static::where('status', 'ready')->count(),
                'delivered' => static::where('status', 'delivered')->count(),
                'cancelled' => static::where('status', 'cancelled')->count(),
                'revenue_today' => static::whereDate('order_date', today())->sum('total_amount'),
                'revenue_month' => static::whereMonth('order_date', now()->month)
                    ->whereYear('order_date', now()->year)
                    ->sum('total_amount'),
                'pending_orders' => static::pending()->count(),
            ];
        });
    }

    // Business Logic
    public function updateStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }

    public function calculateTotalAmount()
    {
        return $this->orderItems->sum('total_price');
    }

    public function getCurrentProductionStage()
    {
        return $this->productionTrackings()
            ->whereNull('completed_at')
            ->latest()
            ->first();
    }
}
