<?php
// app/Models/PatientMeasurement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class PatientMeasurement extends Model
{
    use HasFactory, Cacheable;

    protected $fillable = [
        'uuid',
        'patient_id',
        'consultation_id',
        'measurement_type',
        'measurement_data',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'measurement_data' => 'array',
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

    // Scopes
    public function scopeType($query, $type)
    {
        return $query->where('measurement_type', $type);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByPatient($query, $patientId)
    {
        return $query->where('patient_id', $patientId);
    }

    // Cache Methods
    public static function getCachedByPatient($patientId)
    {
        return Cache::tags(['PatientMeasurement'])->remember("patient.{$patientId}.measurements", 3600, function () use ($patientId) {
            return static::where('patient_id', $patientId)
                ->with(['consultation', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public static function getCachedRecentMeasurements($days = 30)
    {
        return Cache::tags(['PatientMeasurement'])->remember("recent_measurements.{$days}", 1800, function () use ($days) {
            return static::recent($days)
                ->with(['patient', 'consultation'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }
}
