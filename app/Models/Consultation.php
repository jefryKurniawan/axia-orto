<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Traits\Auditable;

class Consultation extends Model
{
    use HasFactory, SoftDeletes, Auditable;

    protected $fillable = [
        'patient_id',
        'doctor_id',
        'consultation_date',
        'complaint',
        'diagnosis',
        'treatment_plan',
        'notes',
        'follow_up_date',
        'status',
        'uuid'
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'follow_up_date' => 'date',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($consultation) {
            $consultation->uuid = (string) \Illuminate\Support\Str::uuid();
        });
    }

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function getStatusBadgeAttribute()
    {
        return match ($this->status) {
            'scheduled' => '<span class="bg-yellow-100 text-yellow-800 text-xs font-medium px-2.5 py-0.5 rounded">Dijadwalkan</span>',
            'in_progress' => '<span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded">Berlangsung</span>',
            'completed' => '<span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded">Selesai</span>',
            'cancelled' => '<span class="bg-red-100 text-red-800 text-xs font-medium px-2.5 py-0.5 rounded">Dibatalkan</span>',
            default => '<span class="bg-gray-100 text-gray-800 text-xs font-medium px-2.5 py-0.5 rounded">Unknown</span>',
        };
    }

    public function getStatusColorAttribute()
    {
        return match ($this->status) {
            'scheduled' => 'yellow',
            'in_progress' => 'blue',
            'completed' => 'green',
            'cancelled' => 'red',
            default => 'gray',
        };
    }

    public function getFormattedDateAttribute()
    {
        return $this->consultation_date->format('d M Y H:i');
    }
}
