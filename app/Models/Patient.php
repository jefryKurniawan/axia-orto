<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Builder;
use App\Models\Traits\Auditable;

class Patient extends Model
{
    use HasFactory, SoftDeletes, Auditable;

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
        'allergies' => 'array',
    ];

    public function getRouteKeyName(): string
    {
        return 'uuid';
    }

    // Relationships
    public function consultations()
    {
        return $this->hasMany(Consultation::class);
    }

    public function treatmentOrders()
    {
        return $this->hasMany(TreatmentOrder::class);
    }

    public function hasActiveOrders(): bool
    {
        return $this->treatmentOrders()
            ->whereNotIn('status', ['completed', 'cancelled'])
            ->exists();
    }

    public function measurements()
    {
        return $this->hasMany(PatientMeasurement::class);
    }

    // Scopes
    public function scopeSearch(Builder $query, ?string $search): Builder
    {
        return $query->when($search, function (Builder $q, string $search) {
            $q->where(function (Builder $inner) use ($search) {
                $inner->where('name', 'like', "%{$search}%")
                    ->orWhere('medical_record_number', 'like', "%{$search}%")
                    ->orWhere('nik', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        });
    }

    public function scopeRecent(Builder $query, int $days = 30): Builder
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }

    // Accessors
    public function getAgeAttribute(): int
    {
        return $this->date_of_birth->age;
    }

    public function getGenderNameAttribute(): string
    {
        return $this->gender === 'L' ? 'Laki-laki' : 'Perempuan';
    }
}
