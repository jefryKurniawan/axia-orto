Ada beberapa masalah yang perlu diperbaiki:

## 1. Masalah: Seeder Class Tidak Ditemukan

Error `Target class [Database\Seeders\UserSeeder] does not exist` menunjukkan bahwa file seeder belum dibuat.

## Solusi: Buat Semua File Seeder

```bash
# Buat semua file seeder yang diperlukan
php artisan make:seeder UserSeeder
php artisan make:seeder ServiceSeeder
php artisan make:seeder PatientSeeder
php artisan make:seeder ConsultationSeeder
php artisan make:seeder PatientMeasurementSeeder
php artisan make:seeder InventoryItemSeeder
php artisan make:seeder TreatmentOrderSeeder
php artisan make:seeder OrderItemSeeder
php artisan make:seeder ProductionTrackingSeeder
php artisan make:seeder PaymentSeeder
```

## 2. Isi Content Seeder

Setelah membuat file seeder, isi dengan kode yang saya berikan sebelumnya. Berikut contoh untuk beberapa seeder utama:

**database/seeders/UserSeeder.php**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'uuid' => Str::uuid(),
                'name' => 'Admin Axia',
                'email' => 'admin@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'specialization' => null,
                'phone' => '081234567890',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Dr. John Doe',
                'email' => 'dr.john@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'specialization' => 'Orthosis & Prosthesis',
                'phone' => '081234567891',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Dr. Jane Smith',
                'email' => 'dr.jane@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'dokter',
                'specialization' => 'Physical Therapy',
                'phone' => '081234567892',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'name' => 'Staf Klinik 1',
                'email' => 'staf1@axia.com',
                'password' => Hash::make('password123'),
                'role' => 'staf_klinik',
                'specialization' => null,
                'phone' => '081234567893',
                'is_active' => true,
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('users')->insert($users);
    }
}
```

**database/seeders/PatientSeeder.php**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PatientSeeder extends Seeder
{
    public function run(): void
    {
        $patients = [
            [
                'uuid' => Str::uuid(),
                'medical_record_number' => 'MRN001',
                'nik' => '1234567890123456',
                'name' => 'Budi Santoso',
                'date_of_birth' => '1980-05-15',
                'gender' => 'L',
                'phone' => '081234567895',
                'address' => 'Jl. Merdeka No. 123, Jakarta',
                'emergency_contact' => '081234567896',
                'insurance_type' => 'bpjs',
                'blood_type' => 'A',
                'allergies' => 'Tidak ada',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'medical_record_number' => 'MRN002',
                'nik' => '1234567890123457',
                'name' => 'Siti Rahayu',
                'date_of_birth' => '1992-08-20',
                'gender' => 'P',
                'phone' => '081234567897',
                'address' => 'Jl. Sudirman No. 456, Jakarta',
                'emergency_contact' => '081234567898',
                'insurance_type' => 'mandiri',
                'blood_type' => 'B',
                'allergies' => 'Debu, Udang',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('patients')->insert($patients);
    }
}
```

**database/seeders/ServiceSeeder.php**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'uuid' => Str::uuid(),
                'code' => 'KONS-001',
                'name' => 'Konsultasi Awal',
                'description' => 'Konsultasi medis awal dengan dokter spesialis',
                'service_type' => 'konsultasi',
                'price' => 150000.00,
                'duration_days' => null,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'uuid' => Str::uuid(),
                'code' => 'ORT-001',
                'name' => 'Orthosis Kaki',
                'description' => 'Pembuatan dan pemasangan orthosis untuk kaki',
                'service_type' => 'ortosis',
                'price' => 2500000.00,
                'duration_days' => 14,
                'is_active' => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ];

        DB::table('services')->insert($services);
    }
}
```

## 3. Pastikan DatabaseSeeder Memanggil Semua Seeder

**database/seeders/DatabaseSeeder.php**

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            UserSeeder::class,
            ServiceSeeder::class,
            PatientSeeder::class,
            ConsultationSeeder::class,
            PatientMeasurementSeeder::class,
            InventoryItemSeeder::class,
            TreatmentOrderSeeder::class,
            OrderItemSeeder::class,
            ProductionTrackingSeeder::class,
            PaymentSeeder::class,
        ]);
    }
}
```

## 4. Update Models dengan UUID Support

Pastikan semua model menggunakan `HasUuids`. Contoh:

**app/Models/User.php**

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids;

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

    public function uniqueIds()
    {
        return ['uuid'];
    }
}
```

Lakukan hal yang sama untuk semua model lainnya.

## 5. Jalankan Ulang Migrasi dan Seeder

```bash
# Reset database dan jalankan migrasi + seeder
php artisan migrate:fresh --seed
```

## 6. Deprecation Warning (Opsional)

Deprecation warning dari `barryvdh/laravel-ide-helper` tidak critical, tapi bisa diperbaiki dengan:

```bash
# Update composer packages
composer update

# Atau nonaktifkan ide-helper sementara di .env
# COMMENT_BEFORE_PRODUCTION=false
```

## 7. Verifikasi

Setelah seeder berhasil, verifikasi data:

```bash
php artisan tinker

>>> \App\Models\User::count()
# Should return 4

>>> \App\Models\Patient::count()
# Should return 2

>>> \App\Models\Service::count()
# Should return 2
```

## 8. Jika Masih Ada Error Foreign Key

Jika ada error foreign key constraint, pastikan urutan seeder benar. Kadang perlu menjalankan seeder tertentu terlebih dahulu:

```php
// Di DatabaseSeeder.php
public function run(): void
{
    $this->call([
        UserSeeder::class, // Harus pertama karena ada foreign key
        ServiceSeeder::class, // Harus sebelum OrderItemSeeder
        PatientSeeder::class, // Harus sebelum ConsultationSeeder
        ConsultationSeeder::class,
        PatientMeasurementSeeder::class,
        InventoryItemSeeder::class,
        TreatmentOrderSeeder::class,
        OrderItemSeeder::class, // Harus setelah ServiceSeeder dan TreatmentOrderSeeder
        ProductionTrackingSeeder::class,
        PaymentSeeder::class,
    ]);
}
```

Dengan langkah-langkah di atas, seharusnya seeder akan berjalan tanpa error. Migrasi sudah berhasil, tinggal membuat dan mengisi file-file seeder yang diperlukan.
