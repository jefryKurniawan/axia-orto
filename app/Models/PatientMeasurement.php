<?php
// app/Models/PatientMeasurement.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\CacheHelper;
use Illuminate\Support\Facades\DB;

class PatientMeasurement extends Model
{
    use HasFactory;

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
        $key = CacheHelper::key('patient_measurements', 'by_patient', ['id' => $patientId]);
        return CacheHelper::remember($key, 3600, function () use ($patientId) {
            return static::where('patient_id', $patientId)
                ->with(['consultation', 'createdBy'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }

    public static function getCachedRecentMeasurements($days = 30)
    {
        $key = CacheHelper::key('patient_measurements', 'recent', ['days' => $days]);
        return CacheHelper::remember($key, 1800, function () use ($days) {
            return static::recent($days)
                ->with(['patient', 'consultation'])
                ->orderBy('created_at', 'desc')
                ->get();
        });
    }
}
