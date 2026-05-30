<?php
// app/Models/Payment.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Auditable;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\DB;

class Payment extends Model
{
    use HasFactory, Auditable;

    protected $fillable = [
        'uuid',
        'treatment_order_id',
        'payment_number',
        'payment_date',
        'payment_method',
        'amount',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(TreatmentOrder::class, 'treatment_order_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Scopes
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    public function scopeByOrder($query, $orderId)
    {
        return $query->where('treatment_order_id', $orderId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('payment_date', '>=', now()->subDays($days));
    }

    // Cache Methods
    public static function getCachedPaymentStats()
    {
        $key = CacheHelper::key('payments', 'stats');
        return CacheHelper::remember($key, 3600, function () {
            return [
                'total_revenue' => static::completed()->sum('amount'),
                'pending_payments' => static::pending()->count(),
                'failed_payments' => static::failed()->count(),
                'today_revenue' => static::completed()->whereDate('payment_date', today())->sum('amount'),
                'month_revenue' => static::completed()
                    ->whereYear('payment_date', now()->year)
                    ->whereMonth('payment_date', now()->month)
                    ->sum('amount'),
                'by_method' => static::selectRaw('payment_method, count(*) as count, sum(amount) as total')
                    ->groupBy('payment_method')
                    ->get(),
            ];
        });
    }

    // Business Logic
    public function updateStatus($status)
    {
        $this->status = $status;
        return $this->save();
    }

    public function isCompleted()
    {
        return $this->status === 'completed';
    }

    public function markAsCompleted()
    {
        return $this->updateStatus('completed');
    }
}
