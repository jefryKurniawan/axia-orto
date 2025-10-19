<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class Consultation extends Model
{
    use HasFactory, Cacheable;

    protected $fillable = [
        'uuid',
        'patient_id',
        'doctor_id',
        'consultation_date',
        'complaint',
        'diagnosis',
        'treatment_plan',
        'notes',
        'follow_up_date',
        'status',
    ];

    protected $casts = [
        'consultation_date' => 'datetime',
        'follow_up_date' => 'date',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function measurements()
    {
        return $this->hasMany(PatientMeasurement::class);
    }

    public function treatmentOrders()
    {
        return $this->hasMany(TreatmentOrder::class);
    }

    // Scopes
    public function scopeScheduled($query)
    {
        return $query->where('status', 'scheduled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('consultation_date', today());
    }

    public function scopeByDoctor($query, $doctorId)
    {
        return $query->where('doctor_id', $doctorId);
    }

    // Cache Methods
    public static function getCachedTodayConsultations()
    {
        return Cache::tags(['Consultation'])->remember('consultations.today', 900, function () {
            return static::today()->with(['patient', 'doctor'])->get();
        });
    }

    public static function getCachedDoctorSchedule($doctorId, $date = null)
    {
        $date = $date ?: today();
        $cacheKey = "doctor_schedule.{$doctorId}.{$date}";

        return Cache::tags(['Consultation'])->remember($cacheKey, 1800, function () use ($doctorId, $date) {
            return static::where('doctor_id', $doctorId)
                ->whereDate('consultation_date', $date)
                ->with('patient')
                ->orderBy('consultation_date')
                ->get();
        });
    }
}
