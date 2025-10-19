<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class Patient extends Model
{
    use HasFactory, Cacheable;

    protected $fillable = [
        'uuid',
        'medical_record_number',
        'nik',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'address',
        'emergency_contact',
        'insurance_type',
        'blood_type',
        'allergies',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    // Relationships
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function treatmentOrders()
    {
        return $this->hasMany(TreatmentOrder::class);
    }

    public function measurements()
    {
        return $this->hasMany(PatientMeasurement::class);
    }

    public function payments()
    {
        return $this->hasManyThrough(Payment::class, TreatmentOrder::class);
    }

    // Scopes
    public function scopeSearch($query, $search)
    {
        return $query->where('name', 'like', "%{$search}%")
            ->orWhere('medical_record_number', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%");
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    public function scopeByInsurance($query, $insuranceType)
    {
        return $query->where('insurance_type', $insuranceType);
    }

    // Cache Methods
    public static function getCachedRecentPatients($days = 30)
    {
        return Cache::tags(['Patient'])->remember("recent_patients.{$days}", 1800, function () use ($days) {
            return static::recent($days)->with(['consultations'])->get();
        });
    }

    public static function getCachedPatientByMRN($mrn)
    {
        return Cache::tags(['Patient'])->remember("patient.mrn.{$mrn}", 3600, function () use ($mrn) {
            return static::where('medical_record_number', $mrn)->first();
        });
    }

    public static function getCachedStats()
    {
        return Cache::tags(['Patient'])->remember('patient_stats', 3600, function () {
            return [
                'total' => static::count(),
                'recent_30_days' => static::recent(30)->count(),
                'by_gender' => static::selectRaw('gender, count(*) as count')
                    ->groupBy('gender')
                    ->pluck('count', 'gender'),
                'by_insurance' => static::selectRaw('insurance_type, count(*) as count')
                    ->groupBy('insurance_type')
                    ->pluck('count', 'insurance_type'),
            ];
        });
    }
}
