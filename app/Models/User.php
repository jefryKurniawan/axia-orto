<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'specialization',
        'phone',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    public function createdMeasurements()
    {
        return $this->hasMany(PatientMeasurement::class, 'created_by');
    }

    public function orders()
    {
        return $this->hasMany(TreatmentOrder::class, 'created_by');
    }

    public function scopeDoctors($query)
    {
        return $query->where('role', 'dokter')->where('is_active', true);
    }

    public function scopeStaff($query)
    {
        return $query->where('role', 'staf_klinik')->where('is_active', true);
    }

    public function getRoleBadgeAttribute()
    {
        return match ($this->role) {
            'admin' => '<span class="bg-blue-500 text-white text-xs font-medium px-2.5 py-0.5 rounded">Admin</span>',
            'dokter' => '<span class="bg-green-500 text-white text-xs font-medium px-2.5 py-0.5 rounded">Dokter</span>',
            'staf_klinik' => '<span class="bg-yellow-500 text-white text-xs font-medium px-2.5 py-0.5 rounded">Staf Klinik</span>',
            default => '<span class="bg-gray-500 text-white text-xs font-medium px-2.5 py-0.5 rounded">Unknown</span>',
        };
    }
}
