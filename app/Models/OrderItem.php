<?php
// app/Models/OrderItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'treatment_order_id',
        'service_id',
        'quantity',
        'unit_price',
        'total_price',
        'specifications',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'specifications' => 'array',
    ];

    // Relationships
    public function order()
    {
        return $this->belongsTo(TreatmentOrder::class, 'treatment_order_id');
    }

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    // Scopes
    public function scopeByOrder($query, $orderId)
    {
        return $query->where('treatment_order_id', $orderId);
    }

    public function scopeByService($query, $serviceId)
    {
        return $query->where('service_id', $serviceId);
    }

    // Business Logic
    public function calculateTotalPrice()
    {
        return $this->quantity * $this->unit_price;
    }

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($model) {
            if (empty($model->total_price)) {
                $model->total_price = $model->calculateTotalPrice();
            }
        });
    }
}
