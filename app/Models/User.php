<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\Cacheable;
use Illuminate\Support\Facades\Cache; // ✅ IMPORT INI
use Illuminate\Support\Facades\DB; // ✅ IMPORT INI untuk raw queries

class User extends Authenticatable
{
    use HasFactory, Notifiable, Cacheable;

    protected $fillable = [
        'uuid',
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
        'password' => 'hashed',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function consultations()
    {
        return $this->hasMany(Consultation::class, 'doctor_id');
    }

    public function treatmentOrders()
    {
        return $this->hasMany(TreatmentOrder::class, 'created_by');
    }

    public function patientMeasurements()
    {
        return $this->hasMany(PatientMeasurement::class, 'created_by');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'created_by');
    }

    public function productionTrackings()
    {
        return $this->hasMany(ProductionTracking::class, 'completed_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDoctors($query)
    {
        return $query->where('role', 'dokter');
    }

    public function scopeStaff($query)
    {
        return $query->where('role', 'staf_klinik');
    }

    public function scopeAdmins($query)
    {
        return $query->where('role', 'admin');
    }

    // Cache Methods
    public static function getCachedActiveDoctors()
    {
        $key = 'user:active_doctors';
        $callback = function () {
            return static::doctors()->active()->get();
        };

        return self::safeCacheRemember(['User'], $key, 3600, $callback);
    }

    public static function getCachedUserStats()
    {
        $key = 'user:user_stats';
        $callback = function () {
            return [
                'total' => static::count(),
                'doctors' => static::doctors()->active()->count(),
                'admins' => static::where('role', 'admin')->active()->count(),
                'staff' => static::where('role', 'staf_klinik')->active()->count(),
            ];
        };

        return self::safeCacheRemember(['User'], $key, 1800, $callback);
    }

    public static function getCachedUsersByRole($role)
    {
        $key = "users_role_{$role}";
        $callback = function () use ($role) {
            return static::where('role', $role)->active()->get();
        };

        return self::safeCacheRemember(['User'], $key, 1800, $callback);
    }

    protected static function safeCacheRemember(array $tags, string $key, int $ttl, callable $callback)
    {
        try {
            // Coba dengan tagging terlebih dahulu
            if (self::supportsTagging()) {
                return Cache::tags($tags)->remember($key, $ttl, $callback);
            } else {
                // Fallback tanpa tagging
                return Cache::remember($key, $ttl, $callback);
            }
        } catch (\Exception $e) {
            // Jika ada error, fallback ke tanpa cache atau tanpa tagging
            \Log::warning("Cache error: {$e->getMessage()}, using fallback");
            return Cache::remember($key, $ttl, $callback);
        }
    }

    protected static function supportsTagging(): bool
    {
        $driver = config('cache.default');
        return in_array($driver, ['redis', 'memcached', 'array']);
    }
}
