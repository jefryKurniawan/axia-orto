Saya akan membantu Anda membuat implementasi lengkap Laravel 10 dengan database `axiadb` dan optimasi cache MySQL. Berikut struktur lengkapnya:

## 🗄️ **KONFIGURASI DATABASE & CACHE**

### 1. **.env Configuration**

```env
APP_NAME="Axia Clinic"
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=axiadb
DB_USERNAME=root
DB_PASSWORD=je540740

CACHE_DRIVER=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis

REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache Settings
QUERY_CACHE_TTL=300
MODEL_CACHE_TTL=1800
```

### 2. **Migration Files**

**Create Users Table**

```bash
php artisan make:migration create_users_table
```

```php
<?php
// database/migrations/2024_01_01_000001_create_users_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('(UUID())'));
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->enum('role', ['admin', 'dokter', 'staf_klinik'])->default('staf_klinik');
            $table->string('specialization')->nullable();
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();

            $table->index(['email', 'role']);
            $table->index(['is_active', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
```

**Create Patients Table**

```php
<?php
// database/migrations/2024_01_01_000002_create_patients_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('(UUID())'));
            $table->string('medical_record_number', 20)->unique();
            $table->string('nik', 16)->nullable();
            $table->string('name');
            $table->date('date_of_birth');
            $table->enum('gender', ['L', 'P']);
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->string('emergency_contact')->nullable();
            $table->enum('insurance_type', ['bpjs', 'mandiri', 'asuransi'])->default('mandiri');
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();
            $table->text('allergies')->nullable();
            $table->timestamps();

            $table->index(['medical_record_number', 'name']);
            $table->index(['phone']);
            $table->index(['created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
```

**Create Consultations Table**

```php
<?php
// database/migrations/2024_01_01_000003_create_consultations_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('(UUID())'));
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');
            $table->foreignId('doctor_id')->constrained('users');
            $table->dateTime('consultation_date');
            $table->text('complaint');
            $table->text('diagnosis');
            $table->text('treatment_plan')->nullable();
            $table->text('notes')->nullable();
            $table->date('follow_up_date')->nullable();
            $table->enum('status', ['scheduled', 'in_progress', 'completed', 'cancelled'])->default('scheduled');
            $table->timestamps();

            $table->index(['patient_id', 'consultation_date']);
            $table->index(['doctor_id', 'status']);
            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('consultations');
    }
};
```

**Create Services Table**

```php
<?php
// database/migrations/2024_01_01_000004_create_services_table.php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique()->default(DB::raw('(UUID())'));
            $table->string('code', 50)->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->enum('service_type', ['konsultasi', 'ortosis', 'protesis', 'terapi', 'alat']);
            $table->decimal('price', 15, 2);
            $table->integer('duration_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['code', 'service_type']);
            $table->index(['is_active', 'service_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('services');
    }
};
```

## 🎯 **MODEL DENGAN CACHE OPTIMIZATION**

### 1. **Cacheable Trait**

```php
<?php
// app/Models/Traits/Cacheable.php

namespace App\Models\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

trait Cacheable
{
    protected static function bootCacheable(): void
    {
        static::created(function (Model $model) {
            $model->clearRelatedCaches();
        });

        static::updated(function (Model $model) {
            $model->clearRelatedCaches();
        });

        static::deleted(function (Model $model) {
            $model->clearRelatedCaches();
        });
    }

    protected function clearRelatedCaches(): void
    {
        $className = class_basename($this);
        Cache::tags([$className])->flush();
    }

    public function scopeCached($query, string $key, int $ttl = 300)
    {
        return Cache::tags([class_basename($this)])->remember($key, $ttl, function () use ($query) {
            return $query->get();
        });
    }

    public function scopeCachedPaginate($query, string $key, int $perPage = 15, int $ttl = 300)
    {
        $page = request()->get('page', 1);
        return Cache::tags([class_basename($this)])->remember(
            "{$key}.page.{$page}",
            $ttl,
            function () use ($query, $perPage) {
                return $query->paginate($perPage);
            }
        );
    }
}
```

### 2. **User Model**

```php
<?php
// app/Models/User.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Traits\Cacheable;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, Cacheable;

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

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeDoctors($query)
    {
        return $query->where('role', 'dokter');
    }

    // Cache Methods
    public static function getCachedActiveDoctors()
    {
        return Cache::tags(['User'])->remember('active_doctors', 3600, function () {
            return static::doctors()->active()->get();
        });
    }

    public static function getCachedUserStats()
    {
        return Cache::tags(['User'])->remember('user_stats', 1800, function () {
            return [
                'total' => static::count(),
                'doctors' => static::doctors()->active()->count(),
                'admins' => static::where('role', 'admin')->active()->count(),
                'staff' => static::where('role', 'staf_klinik')->active()->count(),
            ];
        });
    }
}
```

### 3. **Patient Model dengan Cache Optimized**

```php
<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;

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
```

### 4. **Consultation Model**

```php
<?php
// app/Models/Consultation.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Traits\Cacheable;

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
```

## 🎮 **CONTROLLER DENGAN CACHE STRATEGY**

### 1. **Base Controller**

```php
<?php
// app/Http/Controllers/Api/ApiController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ApiController extends Controller
{
    protected $model;
    protected $cacheKey;
    protected $cacheTtl = 300;

    protected function cachedResponse($key, $callback, $tags = [], $ttl = null)
    {
        $ttl = $ttl ?? $this->cacheTtl;
        $cacheKey = "{$this->cacheKey}.{$key}";

        return Cache::tags($tags)->remember($cacheKey, $ttl, $callback);
    }

    protected function clearCache($tags = [])
    {
        if (!empty($tags)) {
            Cache::tags($tags)->flush();
        }
    }

    protected function successResponse($data, $message = 'Success', $code = 200): JsonResponse
    {
        return response()->json([
            'success' => true,
            'message' => $message,
            'data' => $data,
        ], $code);
    }

    protected function errorResponse($message, $code = 400): JsonResponse
    {
        return response()->json([
            'success' => false,
            'message' => $message,
            'data' => null,
        ], $code);
    }
}
```

### 2. **Patient Controller**

```php
<?php
// app/Http/Controllers/Api/PatientController.php

namespace App\Http\Controllers\Api;

use App\Models\Patient;
use App\Http\Requests\StorePatientRequest;
use App\Http\Requests\UpdatePatientRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PatientController extends ApiController
{
    protected $model = Patient::class;
    protected $cacheKey = 'patients';

    public function __construct()
    {
        $this->cacheTtl = env('QUERY_CACHE_TTL', 300);
    }

    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $search = $request->get('search');

            $cacheKey = "index.page.{$page}.per_page.{$perPage}.search.{$search}";

            $patients = $this->cachedResponse($cacheKey, function () use ($search, $perPage) {
                $query = Patient::with(['consultations']);

                if ($search) {
                    $query->search($search);
                }

                return $query->orderBy('created_at', 'desc')
                            ->paginate($perPage);
            }, ['Patient']);

            return $this->successResponse($patients, 'Patients retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve patients: ' . $e->getMessage(), 500);
        }
    }

    public function store(StorePatientRequest $request)
    {
        try {
            $patient = Patient::create($request->validated());
            $this->clearCache(['Patient']);
            return $this->successResponse($patient, 'Patient created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create patient: ' . $e->getMessage(), 500);
        }
    }

    public function show($id)
    {
        try {
            $patient = $this->cachedResponse("show.{$id}", function () use ($id) {
                return Patient::with([
                    'consultations.doctor',
                    'treatmentOrders',
                    'measurements'
                ])->findOrFail($id);
            }, ['Patient']);

            return $this->successResponse($patient, 'Patient retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Patient not found', 404);
        }
    }

    public function update(UpdatePatientRequest $request, $id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->update($request->validated());
            $this->clearCache(['Patient']);
            return $this->successResponse($patient, 'Patient updated successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to update patient: ' . $e->getMessage(), 500);
        }
    }

    public function destroy($id)
    {
        try {
            $patient = Patient::findOrFail($id);
            $patient->delete();
            $this->clearCache(['Patient']);
            return $this->successResponse(null, 'Patient deleted successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to delete patient: ' . $e->getMessage(), 500);
        }
    }

    public function stats()
    {
        try {
            $stats = $this->cachedResponse('stats', function () {
                return Patient::getCachedStats();
            }, ['Patient'], 3600);

            return $this->successResponse($stats, 'Patient statistics retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve statistics: ' . $e->getMessage(), 500);
        }
    }

    public function search(Request $request)
    {
        try {
            $search = $request->get('q');

            if (!$search) {
                return $this->errorResponse('Search query is required', 400);
            }

            $patients = $this->cachedResponse("search.{$search}", function () use ($search) {
                return Patient::search($search)
                            ->limit(10)
                            ->get(['id', 'medical_record_number', 'name', 'phone']);
            }, ['Patient']);

            return $this->successResponse($patients, 'Search results retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Search failed: ' . $e->getMessage(), 500);
        }
    }
}
```

### 3. **Consultation Controller**

```php
<?php
// app/Http/Controllers/Api/ConsultationController.php

namespace App\Http\Controllers\Api;

use App\Models\Consultation;
use App\Http\Requests\StoreConsultationRequest;
use App\Http\Requests\UpdateConsultationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class ConsultationController extends ApiController
{
    protected $model = Consultation::class;
    protected $cacheKey = 'consultations';

    public function index(Request $request)
    {
        try {
            $page = $request->get('page', 1);
            $perPage = $request->get('per_page', 15);
            $status = $request->get('status');
            $doctorId = $request->get('doctor_id');
            $date = $request->get('date');

            $cacheKey = "index.page.{$page}.status.{$status}.doctor.{$doctorId}.date.{$date}";

            $consultations = $this->cachedResponse($cacheKey, function () use ($status, $doctorId, $date, $perPage) {
                $query = Consultation::with(['patient', 'doctor']);

                if ($status) {
                    $query->where('status', $status);
                }

                if ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }

                if ($date) {
                    $query->whereDate('consultation_date', $date);
                }

                return $query->orderBy('consultation_date', 'desc')
                            ->paginate($perPage);
            }, ['Consultation']);

            return $this->successResponse($consultations, 'Consultations retrieved successfully');

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve consultations: ' . $e->getMessage(), 500);
        }
    }

    public function store(StoreConsultationRequest $request)
    {
        try {
            $consultation = Consultation::create($request->validated());
            $this->clearCache(['Consultation']);
            return $this->successResponse($consultation, 'Consultation created successfully', 201);

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to create consultation: ' . $e->getMessage(), 500);
        }
    }

    public function todaySchedule(Request $request)
    {
        try {
            $doctorId = $request->get('doctor_id');
            $cacheKey = "today_schedule.doctor.{$doctorId}";

            $schedule = $this->cachedResponse($cacheKey, function () use ($doctorId) {
                $query = Consultation::with(['patient'])
                            ->today()
                            ->whereIn('status', ['scheduled', 'in_progress']);

                if ($doctorId) {
                    $query->where('doctor_id', $doctorId);
                }

                return $query->orderBy('consultation_date')->get();
            }, ['Consultation'], 900);

            return $this->successResponse($schedule, "Today's schedule retrieved successfully");

        } catch (\Exception $e) {
            return $this->errorResponse('Failed to retrieve schedule: ' . $e->getMessage(), 500);
        }
    }
}
```

## 🛣️ **ROUTE API**

```php
<?php
// routes/api.php

use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ConsultationController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ServiceController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum', 'cache.headers:public;max_age=300;etag'])->group(function () {

    // Patient Routes
    Route::prefix('patients')->group(function () {
        Route::get('/', [PatientController::class, 'index']);
        Route::post('/', [PatientController::class, 'store']);
        Route::get('/stats', [PatientController::class, 'stats']);
        Route::get('/search', [PatientController::class, 'search']);
        Route::get('/{id}', [PatientController::class, 'show']);
        Route::put('/{id}', [PatientController::class, 'update']);
        Route::delete('/{id}', [PatientController::class, 'destroy']);
    });

    // Consultation Routes
    Route::prefix('consultations')->group(function () {
        Route::get('/', [ConsultationController::class, 'index']);
        Route::post('/', [ConsultationController::class, 'store']);
        Route::get('/today', [ConsultationController::class, 'todaySchedule']);
        Route::get('/{id}', [ConsultationController::class, 'show']);
        Route::put('/{id}', [ConsultationController::class, 'update']);
        Route::patch('/{id}/status', [ConsultationController::class, 'updateStatus']);
        Route::delete('/{id}', [ConsultationController::class, 'destroy']);
    });

    // User Routes
    Route::prefix('users')->group(function () {
        Route::get('/doctors', [UserController::class, 'getDoctors']);
        Route::get('/{id}/schedule', [UserController::class, 'getDoctorSchedule']);
    });

    // Service Routes
    Route::apiResource('services', ServiceController::class);
});

// Public routes with cache
Route::middleware('cache.headers:public;max_age=1800;etag')->group(function () {
    Route::get('/services/active', [ServiceController::class, 'getActiveServices']);
});
```

## 📋 **FORM REQUEST**

```php
<?php
// app/Http/Requests/StorePatientRequest.php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePatientRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'medical_record_number' => 'required|string|max:20|unique:patients',
            'name' => 'required|string|max:255',
            'nik' => 'nullable|string|size:16',
            'date_of_birth' => 'required|date|before:today',
            'gender' => 'required|in:L,P',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'emergency_contact' => 'nullable|string|max:255',
            'insurance_type' => 'required|in:bpjs,mandiri,asuransi',
            'blood_type' => 'nullable|in:A,B,AB,O',
            'allergies' => 'nullable|string',
        ];
    }
}
```

## ⚙️ **CACHE SERVICE PROVIDER**

```php
<?php
// app/Providers/CacheServiceProvider.php

namespace App\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Cache;

class CacheServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Event::listen([
            'eloquent.created: App\Models\Patient',
            'eloquent.updated: App\Models\Patient',
            'eloquent.deleted: App\Models\Patient',
        ], function () {
            Cache::tags(['Patient'])->flush();
        });

        Event::listen([
            'eloquent.created: App\Models\Consultation',
            'eloquent.updated: App\Models\Consultation',
            'eloquent.deleted: App\Models\Consultation',
        ], function () {
            Cache::tags(['Consultation'])->flush();
        });
    }
}
```

## 🚀 **CACHE COMMANDS**

```php
<?php
// app/Console/Commands/WarmCache.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Patient;
use App\Models\Consultation;
use App\Models\User;

class WarmCache extends Command
{
    protected $signature = 'cache:warm';
    protected $description = 'Warm up application cache';

    public function handle(): void
    {
        $this->info('Warming up application cache...');

        // Warm patient caches
        Patient::getCachedStats();
        Patient::getCachedRecentPatients(30);

        // Warm consultation caches
        Consultation::getCachedTodayConsultations();

        // Warm user caches
        User::getCachedActiveDoctors();
        User::getCachedUserStats();

        $this->info('Cache warmed successfully!');
    }
}
```

## 📊 **MYSQL CACHE OPTIMIZATION**

### **MySQL Configuration untuk Beaver**

```sql
-- Optimasi MySQL Cache untuk AxiaDB
SET GLOBAL query_cache_type = 1;
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL innodb_buffer_pool_size = 2147483648; -- 2GB
SET GLOBAL innodb_log_file_size = 268435456; -- 256MB

-- Additional indexes untuk performance
CREATE INDEX idx_patients_created_active ON patients(created_at, insurance_type);
CREATE INDEX idx_consultations_status_date ON consultations(status, consultation_date);
CREATE INDEX idx_users_role_active ON users(role, is_active);

-- Monitor cache performance
SHOW VARIABLES LIKE '%query_cache%';
SHOW STATUS LIKE 'Qcache%';
```

### **Cache Monitoring Query**

```sql
-- Query untuk monitor cache performance di Beaver
SELECT
    VARIABLE_NAME,
    VARIABLE_VALUE
FROM information_schema.GLOBAL_STATUS
WHERE VARIABLE_NAME LIKE 'Qcache%'
   OR VARIABLE_NAME LIKE 'Innodb_buffer_pool%';

-- Query untuk melihat cache hit rate
SELECT
    'Query Cache Hit Rate' AS metric,
    ROUND((Qcache_hits / (Qcache_hits + Qcache_inserts)) * 100, 2) AS value
FROM information_schema.GLOBAL_STATUS
WHERE VARIABLE_NAME = 'Qcache_hits'
UNION ALL
SELECT
    'InnoDB Buffer Pool Hit Rate' AS metric,
    ROUND((1 - (Variable_value / (SELECT Variable_value
        FROM information_schema.GLOBAL_STATUS
        WHERE Variable_name = 'Innodb_buffer_pool_read_requests'))) * 100, 2) AS value
FROM information_schema.GLOBAL_STATUS
WHERE Variable_name = 'Innodb_buffer_pool_reads';
```

## 🎯 **IMPLEMENTASI LANGKAH DEMI LANGKAH**

### **1. Setup Awal**

```bash
# Install dependencies
composer require laravel/sanctum
composer require predis/predis

# Publish sanctum
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"

# Run migrations
php artisan migrate

# Generate app key
php artisan key:generate

# Run cache warming
php artisan cache:warm
```

### **2. Schedule Cache Maintenance**

```php
// app/Console/Kernel.php
protected function schedule(Schedule $schedule)
{
    $schedule->command('cache:warm')->everyFifteenMinutes();
    $schedule->command('cache:clear')->daily();
}
```

### **3. Testing Cache Performance**

```bash
# Test cache hit rates
php artisan tinker
>>> Cache::remember('test', 60, fn() => 'cached_value');
>>> Cache::get('test');
```

Struktur ini memberikan Anda:

-   ✅ Database `axiadb` dengan struktur optimal
-   ✅ Model dengan cache strategy terintegrasi
-   ✅ Controller dengan response caching
-   ✅ Route API yang terorganisir
-   ✅ MySQL cache optimization untuk Beaver
-   ✅ Cache warming dan monitoring
-   ✅ Form request validation

Semua komponen sudah siap untuk diimplementasikan dengan database `axiadb` dan password yang Anda tentukan!
