<!-- RANCANGAN AXIA ERP MVP

Rancangan Module, DB, FrankenPHP Worker Mode, Docker:
Saya akan membuat rancangan database ERP untuk Klinik Ortotik Prostetik yang sangat detail. Berikut adalah implementasi lengkapnya:

## 🏗️ Arsitektur Sistem

### Tech Stack:

-   **Backend**: Laravel 10 + REST API
-   **Database**: MySQL 8.0+ dengan Query Cache
-   **Hashing**: SHA-256 untuk sensitive data
-   **Runtime**: FrankenPHP
-   **Cache**: Redis untuk query caching

## 🗄️ Rancangan Database Schema

### 1. Tabel Master Users & Authentication

```sql
-- Tabel users dengan SHA-256 hashing
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    email_verified_at TIMESTAMP NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dokter', 'terapis', 'staf', 'pasien') NOT NULL,
    specialization VARCHAR(100) NULL, -- untuk dokter/terapis
    license_number VARCHAR(50) NULL, -- nomor lisensi praktik
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    last_login_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_uuid (uuid)
) ENGINE=InnoDB ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;

-- Tabel personal_access_tokens untuk authentication
CREATE TABLE personal_access_tokens (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    tokenable_type VARCHAR(255) NOT NULL,
    tokenable_id BIGINT UNSIGNED NOT NULL,
    name VARCHAR(255) NOT NULL,
    token VARCHAR(64) NOT NULL UNIQUE, -- SHA-256 hashed
    abilities TEXT NULL,
    last_used_at TIMESTAMP NULL,
    expires_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tokenable (tokenable_type, tokenable_id),
    INDEX idx_token (token)
) ENGINE=InnoDB;
```

### 2. Tabel Master Pasien

```sql
-- Tabel patients dengan data lengkap pasien
CREATE TABLE patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    medical_record_number VARCHAR(20) UNIQUE NOT NULL,
    nik VARCHAR(16) NOT NULL UNIQUE,
    name VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    phone VARCHAR(20) NULL,
    email VARCHAR(255) NULL,
    address TEXT NOT NULL,
    province VARCHAR(100) NULL,
    city VARCHAR(100) NULL,
    postal_code VARCHAR(10) NULL,
    emergency_contact_name VARCHAR(255) NULL,
    emergency_contact_phone VARCHAR(20) NULL,
    insurance_type ENUM('bpjs', 'mandiri', 'asuransi', 'lainnya') DEFAULT 'mandiri',
    insurance_number VARCHAR(50) NULL,
    blood_type ENUM('A', 'B', 'AB', 'O') NULL,
    allergies TEXT NULL,
    medical_history TEXT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    updated_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (updated_by) REFERENCES users(id),
    INDEX idx_mrn (medical_record_number),
    INDEX idx_nik (nik),
    INDEX idx_name (name),
    INDEX idx_phone (phone)
) ENGINE=InnoDB;

-- Tabel patient_attachments untuk file pendukung pasien
CREATE TABLE patient_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    patient_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('ktp', 'bpjs', 'surat_rujukan', 'foto', 'xray', 'lainnya') NOT NULL,
    file_size INT NOT NULL,
    file_hash VARCHAR(64) NOT NULL, -- SHA-256 hash
    description TEXT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_file_type (file_type)
) ENGINE=InnoDB;
```

### 3. Tabel Katalog Produk & Layanan

```sql
-- Tabel product_categories
CREATE TABLE product_categories (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    parent_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES product_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_name (name),
    INDEX idx_parent (parent_id)
) ENGINE=InnoDB;

-- Tabel products (ortosis/protesis)
CREATE TABLE products (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    category_id BIGINT UNSIGNED NOT NULL,
    sku VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    product_type ENUM('ortosis', 'protesis', 'alat_bantu', 'konsultasi', 'terapi') NOT NULL,
    unit_price DECIMAL(15,2) NOT NULL,
    cost_price DECIMAL(15,2) NOT NULL,
    is_taxable BOOLEAN DEFAULT TRUE,
    tax_rate DECIMAL(5,2) DEFAULT 0,
    is_active BOOLEAN DEFAULT TRUE,
    specifications JSON NULL, -- spesifikasi teknis
    manufacturing_time INT NULL, -- waktu pembuatan dalam hari
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES product_categories(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_sku (sku),
    INDEX idx_name (name),
    INDEX idx_type (product_type),
    INDEX idx_category (category_id)
) ENGINE=InnoDB;

-- Tabel product_components untuk BOM (Bill of Materials)
CREATE TABLE ProductComponent
 (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    product_id BIGINT UNSIGNED NOT NULL,
    component_name VARCHAR(255) NOT NULL,
    material_type VARCHAR(100) NOT NULL,
    quantity DECIMAL(8,2) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(10,2) NOT NULL,
    specifications JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;
```

### 4. Tabel Inventory & Stok

```sql
-- Tabel warehouses
CREATE TABLE warehouses (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    location VARCHAR(500) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_name (name)
) ENGINE=InnoDB;

-- Tabel inventory_items
CREATE TABLE inventory_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    warehouse_id BIGINT UNSIGNED NOT NULL,
    item_code VARCHAR(50) UNIQUE NOT NULL,
    item_name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    category VARCHAR(100) NOT NULL,
    unit VARCHAR(20) NOT NULL,
    current_stock DECIMAL(10,2) DEFAULT 0,
    min_stock DECIMAL(10,2) DEFAULT 0,
    max_stock DECIMAL(10,2) DEFAULT 0,
    unit_cost DECIMAL(10,2) NOT NULL,
    total_value DECIMAL(15,2) DEFAULT 0,
    supplier_id BIGINT UNSIGNED NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_item_code (item_code),
    INDEX idx_category (category),
    INDEX idx_warehouse (warehouse_id)
) ENGINE=InnoDB;

-- Tabel inventory_transactions
CREATE TABLE inventory_transactions (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    inventory_item_id BIGINT UNSIGNED NOT NULL,
    transaction_type ENUM('masuk', 'keluar', 'adjustment', 'production') NOT NULL,
    quantity DECIMAL(10,2) NOT NULL,
    unit_cost DECIMAL(10,2) NOT NULL,
    total_cost DECIMAL(15,2) NOT NULL,
    reference_type ENUM('purchase', 'production', 'sales', 'adjustment') NULL,
    reference_id BIGINT UNSIGNED NULL,
    notes TEXT NULL,
    transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_item_id (inventory_item_id),
    INDEX idx_transaction_type (transaction_type),
    INDEX idx_transaction_date (transaction_date)
) ENGINE=InnoDB;
```

### 5. Tabel Pemeriksaan & Assessment

```sql
-- Tabel medical_assessments
CREATE TABLE medical_assessments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    patient_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED NOT NULL,
    assessment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    diagnosis TEXT NOT NULL,
    condition_description TEXT NOT NULL,
    measurement_data JSON NOT NULL, -- data pengukuran dalam JSON
    recommended_device VARCHAR(500) NULL,
    treatment_plan TEXT NULL,
    notes TEXT NULL,
    follow_up_date DATE NULL,
    status ENUM('draft', 'completed', 'cancelled') DEFAULT 'draft',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    INDEX idx_patient_id (patient_id),
    INDEX idx_doctor_id (doctor_id),
    INDEX idx_assessment_date (assessment_date)
) ENGINE=InnoDB;

-- Tabel assessment_attachments
CREATE TABLE assessment_attachments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    assessment_id BIGINT UNSIGNED NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(500) NOT NULL,
    file_type ENUM('foto', 'xray', 'scan', 'dokumen') NOT NULL,
    file_size INT NOT NULL,
    description TEXT NULL,
    uploaded_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (assessment_id) REFERENCES medical_assessments(id) ON DELETE CASCADE,
    FOREIGN KEY (uploaded_by) REFERENCES users(id),
    INDEX idx_assessment_id (assessment_id)
) ENGINE=InnoDB;
```

### 6. Tabel Orders & Production

```sql
-- Tabel orders
CREATE TABLE orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    order_number VARCHAR(50) UNIQUE NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    assessment_id BIGINT UNSIGNED NULL,
    order_date DATE NOT NULL,
    delivery_date DATE NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    grand_total DECIMAL(15,2) NOT NULL,
    status ENUM('draft', 'confirmed', 'in_production', 'quality_check', 'ready', 'delivered', 'cancelled') DEFAULT 'draft',
    payment_status ENUM('pending', 'partial', 'paid', 'overdue') DEFAULT 'pending',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    approved_by BIGINT UNSIGNED NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (assessment_id) REFERENCES medical_assessments(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    FOREIGN KEY (approved_by) REFERENCES users(id),
    INDEX idx_order_number (order_number),
    INDEX idx_patient_id (patient_id),
    INDEX idx_status (status),
    INDEX idx_order_date (order_date)
) ENGINE=InnoDB;

-- Tabel order_items
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    order_id BIGINT UNSIGNED NOT NULL,
    product_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL,
    discount_percentage DECIMAL(5,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    specifications JSON NULL, -- spesifikasi custom untuk item
    notes TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id),
    INDEX idx_order_id (order_id),
    INDEX idx_product_id (product_id)
) ENGINE=InnoDB;

-- Tabel production_orders
CREATE TABLE production_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    order_id BIGINT UNSIGNED NOT NULL,
    production_number VARCHAR(50) UNIQUE NOT NULL,
    start_date DATE NULL,
    expected_finish_date DATE NULL,
    actual_finish_date DATE NULL,
    status ENUM('pending', 'in_progress', 'quality_check', 'completed', 'cancelled') DEFAULT 'pending',
    assigned_to BIGINT UNSIGNED NULL, -- teknisi
    progress_percentage DECIMAL(5,2) DEFAULT 0,
    quality_notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (assigned_to) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_production_number (production_number),
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

### 7. Tabel Pembayaran & Keuangan

```sql
-- Tabel payments
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),saya ada error "Undefined type 'App\Models\InventoryItem'." di InventoryTransaction.php di code "return $this->belongsTo(InventoryItem::class, 'inventoryu_item_id');", dan ada error "Undefined type 'App\Models\InventoryItem'." pada InventoryTransactionController.php di code " $item = InventoryItem::findOrFail($validatedData['inventory_item_id']);", dan ada error juga ini "Undefined type 'App\Models\Supplier'." di file InventoryIte.php pada baris ini "return $this->belongsTo(Supplier::class, 'supplier_id');
}" dan lagi error ini "Undefined type 'App\Models\InventoryItem'." pada file ini nventoryItemController.php dengan rata-rata error seperti ini "Undefined type 'App\Models\InventoryItem'." bantu saya kenapa error apakah ada yang salah pada code saya atau bagaimana

    payment_number VARCHAR(50) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'transfer', 'debit_card', 'credit_card', 'bpjs') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reference_number VARCHAR(100) NULL, -- untuk transfer/kartu
    notes TEXT NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
    verified_by BIGINT UNSIGNED NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (verified_by) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_payment_number (payment_number),
    INDEX idx_order_id (order_id),
    INDEX idx_payment_date (payment_date)
) ENGINE=InnoDB;

-- Tabel invoices
CREATE TABLE invoices (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    invoice_number VARCHAR(50) UNIQUE NOT NULL,
    order_id BIGINT UNSIGNED NOT NULL,
    invoice_date DATE NOT NULL,
    due_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    remaining_amount DECIMAL(15,2) NOT NULL,
    status ENUM('draft', 'sent', 'overdue', 'paid', 'cancelled') DEFAULT 'draft',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES orders(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_invoice_number (invoice_number),
    INDEX idx_order_id (order_id),
    INDEX idx_status (status)
) ENGINE=InnoDB;
```

## 🔧 Konfigurasi Laravel & MySQL

### 1. Konfigurasi Database (.env)

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=erp_ortopro
DB_USERNAME=erp_user
DB_PASSWORD=secure_password_here

# Cache Configuration
CACHE_DRIVER=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Hashing Configuration
HASH_DRIVER=sha256
```

### 2. Model Configuration Example

```php
<?php
// app/Models/Patient.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Patient extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'uuid',
        'medical_record_number',
        'nik',
        'name',
        'date_of_birth',
        'gender',
        'phone',
        'email',
        'address',
        'province',
        'city',
        'postal_code',
        'emergency_contact_name',
        'emergency_contact_phone',
        'insurance_type',
        'insurance_number',
        'blood_type',
        'allergies',
        'medical_history',
        'is_active',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'allergies' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->uuid)) {
                $model->uuid = Str::uuid();
            }
            if (empty($model->medical_record_number)) {
                $model->medical_record_number = static::generateMRN();
            }
        });
    }

    public static function generateMRN()
    {
        $prefix = 'MRN';
        $year = date('Y');
        $lastPatient = static::whereYear('created_at', $year)->latest()->first();

        $sequence = $lastPatient ?
            (int) substr($lastPatient->medical_record_number, -6) + 1 : 1;

        return $prefix . $year . str_pad($sequence, 6, '0', STR_PAD_LEFT);
    }

    // Relationships
    public function assessments()
    {
        return $this->hasMany(MedicalAssessment::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function attachments()
    {
        return $this->hasMany(PatientAttachment::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}
```

### 3. Repository dengan Query Caching

```php
<?php
// app/Repositories/PatientRepository.php

namespace App\Repositories;

use App\Models\Patient;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class PatientRepository
{
    protected $cacheTime = 3600; // 1 hour

    public function getWithRelations($uuid)
    {
        $cacheKey = "patient.{$uuid}.with_relations";

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($uuid) {
            return Patient::with([
                'assessments.doctor',
                'orders.orderItems.product',
                'attachments',
                'creator',
                'updater'
            ])->where('uuid', $uuid)->firstOrFail();
        });
    }

    public function getPaginated($filters = [], $perPage = 15)
    {
        $cacheKey = 'patients.paginated.' . md5(serialize($filters) . $perPage);

        return Cache::remember($cacheKey, $this->cacheTime, function () use ($filters, $perPage) {
            $query = Patient::with(['creator', 'updater']);

            if (!empty($filters['search'])) {
                $query->where(function ($q) use ($filters) {
                    $q->where('name', 'like', "%{$filters['search']}%")
                      ->orWhere('medical_record_number', 'like', "%{$filters['search']}%")
                      ->orWhere('nik', 'like', "%{$filters['search']}%");
                });
            }

            if (!empty($filters['gender'])) {
                $query->where('gender', $filters['gender']);
            }

            if (!empty($filters['insurance_type'])) {
                $query->where('insurance_type', $filters['insurance_type']);
            }

            return $query->orderBy('created_at', 'desc')->paginate($perPage);
        });
    }

    public function getStatistics()
    {
        return Cache::remember('patients.statistics', $this->cacheTime, function () {
            return [
                'total' => Patient::count(),
                'active' => Patient::where('is_active', true)->count(),
                'by_gender' => Patient::select('gender', DB::raw('count(*) as count'))
                    ->groupBy('gender')
                    ->get()
                    ->pluck('count', 'gender'),
                'by_insurance' => Patient::select('insurance_type', DB::raw('count(*) as count'))
                    ->groupBy('insurance_type')
                    ->get()
                    ->pluck('count', 'insurance_type'),
                'monthly_registrations' => Patient::select(
                    DB::raw('YEAR(created_at) as year'),
                    DB::raw('MONTH(created_at) as month'),
                    DB::raw('COUNT(*) as count')
                )
                ->where('created_at', '>=', now()->subYear())
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get()
            ];
        });
    }
}
```

## 🎯 Modul MVP (Minimum Viable Product)

### 1. **Modul Manajemen Pasien**

-   Pendaftaran pasien baru
-   Pencarian dan filter pasien
-   Update data pasien
-   Upload dokumen pendukung
-   Riwayat kunjungan

### 2. **Modul Pemeriksaan Medis**

-   Input assessment dokter
-   Data pengukuran ortotik/protesis
-   Rekomendasi alat
-   Rencana perawatan
-   Foto dan dokumentasi

### 3. **Modul Order & Produksi**

-   Buat order dari assessment
-   Tracking status produksi
-   Manajemen jadwal produksi
-   Quality control

### 4. **Modul Inventory**

-   Stok bahan baku
-   Tracking masuk/keluar
-   Alert stok minimum
-   Valuation inventory

### 5. **Modul Pembayaran**

-   Generate invoice
-   Tracking pembayaran
-   Laporan keuangan
-   Integrasi BPJS (jika ada)

### 6. **Modul Laporan & Analytics**

-   Laporan pasien
-   Laporan produksi
-   Laporan keuangan
-   Statistik kinerja

## 🔒 Security Implementation

### 1. SHA-256 Hashing Configuration

```php
<?php
// app/Services/SHA256Hasher.php

namespace App\Services;

use Illuminate\Contracts\Hashing\Hasher;

class SHA256Hasher implements Hasher
{
    public function info($hashedValue)
    {
        return [
            'algo' => 'sha256',
            'algoName' => 'sha256',
        ];
    }

    public function make($value, array $options = [])
    {
        $salt = $options['salt'] ?? config('app.key');
        return hash('sha256', $value . $salt);
    }

    public function check($value, $hashedValue, array $options = [])
    {
        if (strlen($hashedValue) === 0) {
            return false;
        }

        $salt = $options['salt'] ?? config('app.key');
        return hash_equals($hashedValue, $this->make($value, ['salt' => $salt]));
    }

    public function needsRehash($hashedValue, array $options = [])
    {
        return false;
    }
}
```

### 2. MySQL Query Cache Optimization

```sql
-- Konfigurasi MySQL untuk optimal performance
SET GLOBAL query_cache_type = 1;
SET GLOBAL query_cache_size = 67108864; -- 64MB
SET GLOBAL innodb_buffer_pool_size = 2147483648; -- 2GB
SET GLOBAL innodb_log_file_size = 268435456; -- 256MB

-- Optimasi tabel dengan compression
ALTER TABLE patients ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;
ALTER TABLE medical_assessments ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;
ALTER TABLE orders ROW_FORMAT=COMPRESSED KEY_BLOCK_SIZE=8;
```

## 🚀 Deployment dengan FrankenPHP

### 1. Docker Configuration

```dockerfile
FROM dunglas/frankenphp:latest

# Install PHP extensions
RUN install-php-extensions \
    pdo_mysql \
    redis \
    gd \
    zip

# Copy application
COPY . /app
COPY ./.frankenphp /app/.frankenphp

# Set permissions
RUN chown -R www-data:www-data /app/storage
RUN chown -R www-data:www-data /app/bootstrap/cache

EXPOSE 80
EXPOSE 443
```

### 2. FrankenPHP Configuration

```yaml
# .frankenphp/frankenphp.yml

server:
    host: "0.0.0.0"
    port: 80

    workers:
        - count: 4
          protocol: "http"

php:
    expose_php: false
    memory_limit: "256M"
    max_execution_time: 60
    upload_max_filesize: "64M"
    post_max_size: "64M"

    ini:
        session.save_handler: redis
        session.save_path: "tcp://redis:6379"
        opcache.enable: 1
        opcache.memory_consumption: 256
        opcache.max_accelerated_files: 20000

log:
    level: error
```

Implementasi ini memberikan fondasi yang kuat untuk ERP Klinik Ortotik Prostetik dengan performa tinggi melalui caching dan optimasi database, keamanan dengan SHA-256, serta skalabilitas dengan FrankenPHP.

DB :
MariaDB [(none)]> use axiadb;
ERROR 1049 (42000): Unknown database 'axiadb'
MariaDB [(none)]> creat database axiadb;
ERROR 1064 (42000): You have an error in your SQL syntax; check the manual that corresponds to your MariaDB server version for the right syntax to use near 'creat database axiadb' at line 1
MariaDB [(none)]> CREATE DATABASE axiadb;
Query OK, 1 row affected (0.001 sec)

MariaDB [(none)]> CREATE TABLE users (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> -- UUID() menghasilkan Unique Identifier untuk URL atau API
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> name VARCHAR(255) NOT NULL,
-> email VARCHAR(255) UNIQUE NOT NULL,
-> email_verified_at TIMESTAMP NULL,
-> -- Disarankan untuk menggunakan VARCHAR(255) untuk Bcrypt/Argon2 hashing
-> password VARCHAR(255) NOT NULL,
-> role ENUM('admin', 'dokter', 'terapis', 'staf', 'pasien') NOT NULL,
-> specialization VARCHAR(100) NULL,
-> license_number VARCHAR(50) NULL,
-> phone VARCHAR(20) NULL,
-> address TEXT NULL,
-> is_active BOOLEAN DEFAULT TRUE,
-> last_login_at TIMESTAMP NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
-> INDEX idx_email (email),
-> INDEX idx_role (role),
-> INDEX idx_uuid (uuid)
-> ) ENGINE=InnoDB;
ERROR 1046 (3D000): No database selected
MariaDB [(none)]> USE axiadb
Database changed
MariaDB [axiadb]> USE axiadb;
Database changed
MariaDB [axiadb]> CREATE TABLE users ( id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY, uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()), name VARCHAR(255) NOT NULL, email VARCHAR(255) UNIQUE NOT NULL, email_verified_at TIMESTAMP NULL, password VARCHAR(255) NOT NULL, role ENUM('admin', 'dokter', 'terapis', 'staf', 'pasien') NOT NULL, specialization VARCHAR(100) NULL, license_number VARCHAR(50) NULL, phone VARCHAR(20) NULL, address TEXT NULL, is_active BOOLEAN DEFAULT TRUE, last_login_at TIMESTAMP
NULL, created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP, updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP, INDEX idx_email (email), INDEX idx_role (role), INDEX idx_uuid (uuid) ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.119 sec)

MariaDB [axiadb]> CREATE TABLE personal_access_tokens (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> -- Polymorphic relationship (Type: 'App\Models\User', ID: user_id)
-> tokenable_type VARCHAR(255) NOT NULL,
-> tokenable_id BIGINT UNSIGNED NOT NULL,
-> name VARCHAR(255) NOT NULL,
-> -- VARCHAR(64) sudah tepat untuk menyimpan hash token SHA-256
-> token VARCHAR(64) NOT NULL UNIQUE,
-> abilities TEXT NULL,
-> last_used_at TIMESTAMP NULL,
-> expires_at TIMESTAMP NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
-> -- Index untuk pencarian cepat pemilik token
-> INDEX idx_tokenable (tokenable_type, tokenable_id),
-> INDEX idx_token (token)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.086 sec)

MariaDB [axiadb]> select _ from users
-> ^C
MariaDB [axiadb]> use axiadb;
Database changed
MariaDB [axiadb]> select _ from users;
Empty set (0.048 sec)

MariaDB [axiadb]> use axiadb;
Database changed
MariaDB [axiadb]> CREATE TABLE patients (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> -- UUID untuk akses eksternal yang aman
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> medical_record_number VARCHAR(20) UNIQUE NOT NULL,
-> -- Nomor Induk Kependudukan (NIK)
-> nik VARCHAR(16) NOT NULL UNIQUE,
-> name VARCHAR(255) NOT NULL,
-> date_of_birth DATE NOT NULL,
-> gender ENUM('L', 'P') NOT NULL,
-> phone VARCHAR(20) NULL,
-> email VARCHAR(255) NULL,
-> address TEXT NOT NULL,
-> province VARCHAR(100) NULL,
-> city VARCHAR(100) NULL,
-> postal_code VARCHAR(10) NULL,
-> emergency_contact_name VARCHAR(255) NULL,
-> emergency_contact_phone VARCHAR(20) NULL,
-> insurance_type ENUM('bpjs', 'mandiri', 'asuransi', 'lainnya') DEFAULT 'mandiri',
-> insurance_number VARCHAR(50) NULL,
-> blood_type ENUM('A', 'B', 'AB', 'O') NULL,
-> allergies TEXT NULL,
-> medical_history TEXT NULL,
-> is_active BOOLEAN DEFAULT TRUE,
->
 -> -- Kolom Audit: FOREIGN KEY ke tabel users(id)
-> created_by BIGINT UNSIGNED NULL,
-> updated_by BIGINT UNSIGNED NULL,
->
 -> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (created_by) REFERENCES users(id),
-> FOREIGN KEY (updated_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_mrn (medical_record_number),
-> INDEX idx_nik (nik),
-> INDEX idx_name (name),
-> INDEX idx_phone (phone)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.140 sec)

MariaDB [axiadb]> CREATE TABLE patient_attachments (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> patient_id BIGINT UNSIGNED NOT NULL,
-> file_name VARCHAR(255) NOT NULL,
-> -- Path lengkap di server penyimpanan
-> file_path VARCHAR(500) NOT NULL,
-> file_type ENUM('ktp', 'bpjs', 'surat_rujukan', 'foto', 'xray', 'lainnya') NOT NULL,
-> file_size INT NOT NULL, -- Ukuran file dalam bytes
-> file_hash VARCHAR(64) NOT NULL, -- SHA-256 hash untuk verifikasi integritas
-> description TEXT NULL,
-> uploaded_by BIGINT UNSIGNED NOT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
-> FOREIGN KEY (uploaded_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_patient_id (patient_id),
-> INDEX idx_file_type (file_type)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.093 sec)

MariaDB [axiadb]> CREATE TABLE product_categories (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> name VARCHAR(255) NOT NULL,
-> description TEXT NULL,
-> -- parent_id memungkinkan kategori bersarang (nested/hierarchical categories)
-> parent_id BIGINT UNSIGNED NULL,
-> is_active BOOLEAN DEFAULT TRUE,
-> created_by BIGINT UNSIGNED NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (parent_id) REFERENCES product_categories(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_name (name),
-> INDEX idx_parent (parent_id)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.114 sec)

MariaDB [axiadb]> CREATE TABLE products (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> category_id BIGINT UNSIGNED NOT NULL,
-> sku VARCHAR(50) UNIQUE NOT NULL, -- Stock Keeping Unit
-> name VARCHAR(255) NOT NULL,
-> description TEXT NULL,
-> product_type ENUM('ortosis', 'protesis', 'alat_bantu', 'konsultasi', 'terapi') NOT NULL,
-> unit_price DECIMAL(15,2) NOT NULL, -- Harga jual
-> cost_price DECIMAL(15,2) NOT NULL, -- Harga modal/produksi
-> is_taxable BOOLEAN DEFAULT TRUE,
-> tax_rate DECIMAL(5,2) DEFAULT 0,
-> is_active BOOLEAN DEFAULT TRUE,
-> specifications JSON NULL, -- spesifikasi teknis (mis. ukuran, material standar)
-> manufacturing_time INT NULL, -- waktu pembuatan dalam hari
-> created_by BIGINT UNSIGNED NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (category_id) REFERENCES product_categories(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_sku (sku),
-> INDEX idx_name (name),
-> INDEX idx_type (product_type),
-> INDEX idx_category (category_id)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.151 sec)

MariaDB [axiadb]> CREATE TABLE product_components (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> product_id BIGINT UNSIGNED NOT NULL,
-> component_name VARCHAR(255) NOT NULL,
-> material_type VARCHAR(100) NOT NULL,
-> quantity DECIMAL(8,2) NOT NULL,
-> unit VARCHAR(20) NOT NULL,
-> unit_cost DECIMAL(10,2) NOT NULL,
-> total_cost DECIMAL(10,2) NOT NULL,
-> specifications JSON NULL, -- spesifikasi komponen tertentu
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Key
-> FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
->
 -> -- Index
-> INDEX idx_product_id (product_id)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.094 sec)

MariaDB [axiadb]> CREATE TABLE suppliers (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> name VARCHAR(255) NOT NULL,
-> contact_person VARCHAR(100) NULL,
-> phone VARCHAR(20) NULL,
-> email VARCHAR(255) UNIQUE NULL,
-> address TEXT NULL,
-> is_active BOOLEAN DEFAULT TRUE,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
-> INDEX idx_supplier_name (name)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.147 sec)

MariaDB [axiadb]> CREATE TABLE warehouses (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> name VARCHAR(255) NOT NULL,
-> location VARCHAR(500) NULL,
-> is_active BOOLEAN DEFAULT TRUE,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
-> INDEX idx_name (name)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.209 sec)

MariaDB [axiadb]> CREATE TABLE inventory_items (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> warehouse_id BIGINT UNSIGNED NOT NULL,
-> item_code VARCHAR(50) UNIQUE NOT NULL,
-> item_name VARCHAR(255) NOT NULL,
-> description TEXT NULL,
-> category VARCHAR(100) NOT NULL,
-> unit VARCHAR(20) NOT NULL,
-> current_stock DECIMAL(10,2) DEFAULT 0,
-> min_stock DECIMAL(10,2) DEFAULT 0,
-> max_stock DECIMAL(10,2) DEFAULT 0,
-> unit_cost DECIMAL(10,2) NOT NULL,
-> total_value DECIMAL(15,2) DEFAULT 0,
-> supplier_id BIGINT UNSIGNED NULL,
-> is_active BOOLEAN DEFAULT TRUE,
-> created_by BIGINT UNSIGNED NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (warehouse_id) REFERENCES warehouses(id),
-> FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_item_code (item_code),
-> INDEX idx_category (category),
-> INDEX idx_warehouse (warehouse_id)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.150 sec)

MariaDB [axiadb]> CREATE TABLE inventory_transactions (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> inventory_item_id BIGINT UNSIGNED NOT NULL,
-> transaction_type ENUM('masuk', 'keluar', 'adjustment', 'production') NOT NULL,
-> quantity DECIMAL(10,2) NOT NULL,
-> unit_cost DECIMAL(10,2) NOT NULL,
-> total_cost DECIMAL(15,2) NOT NULL,
-> -- Referensi ke dokumen sumber (misalnya ID PO, ID Produksi, ID Penjualan)
-> reference_type ENUM('purchase', 'production', 'sales', 'adjustment') NULL,
-> reference_id BIGINT UNSIGNED NULL,
-> notes TEXT NULL,
-> transaction_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> created_by BIGINT UNSIGNED NOT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (inventory_item_id) REFERENCES inventory_items(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_item_id (inventory_item_id),
-> INDEX idx_transaction_type (transaction_type),
-> INDEX idx_transaction_date (transaction_date)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.118 sec)

MariaDB [axiadb]> CREATE TABLE medical_assessments (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> patient_id BIGINT UNSIGNED NOT NULL,
-> -- doctor_id merujuk ke users.id dengan role 'dokter' atau 'terapis'
-> doctor_id BIGINT UNSIGNED NOT NULL,
-> assessment_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> diagnosis TEXT NOT NULL,
-> condition_description TEXT NOT NULL,
-> -- Data pengukuran spesifik (misalnya, panjang kaki, dimensi, dll.)
-> measurement_data JSON NOT NULL,
-> recommended_device VARCHAR(500) NULL,
-> treatment_plan TEXT NULL,
-> notes TEXT NULL,
-> follow_up_date DATE NULL,
-> status ENUM('draft', 'completed', 'cancelled') DEFAULT 'draft',
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (patient_id) REFERENCES patients(id),
-> -- Menggunakan users(id) karena dokter/terapis adalah pengguna sistem
-> FOREIGN KEY (doctor_id) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_patient_id (patient_id),
-> INDEX idx_doctor_id (doctor_id),
-> INDEX idx_assessment_date (assessment_date)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.111 sec)

MariaDB [axiadb]> CREATE TABLE assessment_attachments (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> assessment_id BIGINT UNSIGNED NOT NULL,
-> file_name VARCHAR(255) NOT NULL,
-> file_path VARCHAR(500) NOT NULL,
-> file_type ENUM('foto', 'xray', 'scan', 'dokumen') NOT NULL,
-> file_size INT NOT NULL,
-> description TEXT NULL,
-> uploaded_by BIGINT UNSIGNED NOT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (assessment_id) REFERENCES medical_assessments(id) ON DELETE CASCADE,
-> FOREIGN KEY (uploaded_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_assessment_id (assessment_id)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.127 sec)

MariaDB [axiadb]> CREATE TABLE orders (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> order_number VARCHAR(50) UNIQUE NOT NULL,
-> patient_id BIGINT UNSIGNED NOT NULL,
-> assessment_id BIGINT UNSIGNED NULL, -- Opsional: Referensi ke penilaian medis sumber
-> order_date DATE NOT NULL,
-> delivery_date DATE NULL,
-> total_amount DECIMAL(15,2) NOT NULL, -- Total harga item sebelum pajak/diskon
-> tax_amount DECIMAL(15,2) DEFAULT 0,
-> discount_amount DECIMAL(15,2) DEFAULT 0,
-> grand_total DECIMAL(15,2) NOT NULL, -- Total akhir yang harus dibayar
-> status ENUM('draft', 'confirmed', 'in_production', 'quality_check', 'ready', 'delivered', 'cancelled') DEFAULT 'draft',
-> payment_status ENUM('pending', 'partial', 'paid', 'overdue') DEFAULT 'pending',
-> notes TEXT NULL,
-> created_by BIGINT UNSIGNED NOT NULL,
-> approved_by BIGINT UNSIGNED NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (patient_id) REFERENCES patients(id),
-> FOREIGN KEY (assessment_id) REFERENCES medical_assessments(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
-> FOREIGN KEY (approved_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_order_number (order_number),
-> INDEX idx_patient_id (patient_id),
-> INDEX idx_status (status),
-> INDEX idx_order_date (order_date)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.191 sec)

MariaDB [axiadb]> CREATE TABLE order_items (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> order_id BIGINT UNSIGNED NOT NULL,
-> product_id BIGINT UNSIGNED NOT NULL,
-> quantity INT NOT NULL DEFAULT 1,
-> unit_price DECIMAL(15,2) NOT NULL,
-> discount_percentage DECIMAL(5,2) DEFAULT 0,
-> discount_amount DECIMAL(15,2) DEFAULT 0,
-> tax_amount DECIMAL(15,2) DEFAULT 0,
-> total_amount DECIMAL(15,2) NOT NULL, -- Harga item setelah diskon/pajak
-> specifications JSON NULL, -- Spesifikasi kustom yang spesifik untuk pesanan ini (misal: "warna: biru", "tinggi: 15cm")
-> notes TEXT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
-> FOREIGN KEY (product_id) REFERENCES products(id),
->
 -> -- Indexes
-> INDEX idx_order_id (order_id),
-> INDEX idx_product_id (product_id)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.120 sec)

MariaDB [axiadb]> CREATE TABLE production_orders (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> order_id BIGINT UNSIGNED NOT NULL,
-> production_number VARCHAR(50) UNIQUE NOT NULL,
-> start_date DATE NULL,
-> expected_finish_date DATE NULL,
-> actual_finish_date DATE NULL,
-> status ENUM('pending', 'in_progress', 'quality_check', 'completed', 'cancelled') DEFAULT 'pending',
-> assigned_to BIGINT UNSIGNED NULL, -- Teknisi atau Terapis yang bertanggung jawab
-> progress_percentage DECIMAL(5,2) DEFAULT 0,
-> quality_notes TEXT NULL,
-> created_by BIGINT UNSIGNED NOT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (order_id) REFERENCES orders(id),
-> FOREIGN KEY (assigned_to) REFERENCES users(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_production_number (production_number),
-> INDEX idx_order_id (order_id),
-> INDEX idx_status (status)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.163 sec)

MariaDB [axiadb]> CREATE TABLE payments (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> payment_number VARCHAR(50) UNIQUE NOT NULL,
-> order_id BIGINT UNSIGNED NOT NULL, -- Merujuk ke pesanan yang dibayar
-> payment_date DATE NOT NULL,
-> payment_method ENUM('cash', 'transfer', 'debit_card', 'credit_card', 'bpjs') NOT NULL,
-> amount DECIMAL(15,2) NOT NULL,
-> reference_number VARCHAR(100) NULL, -- Nomor referensi unik (misalnya, nomor transfer bank)
-> notes TEXT NULL,
-> status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'pending',
-> verified_by BIGINT UNSIGNED NULL, -- Siapa yang memverifikasi pembayaran (dari tabel users)
-> created_by BIGINT UNSIGNED NOT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (order_id) REFERENCES orders(id),
-> FOREIGN KEY (verified_by) REFERENCES users(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_payment_number (payment_number),
-> INDEX idx_order_id (order_id),
-> INDEX idx_payment_date (payment_date)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.219 sec)

MariaDB [axiadb]> CREATE TABLE invoices (
-> id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
-> uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
-> invoice_number VARCHAR(50) UNIQUE NOT NULL,
-> order_id BIGINT UNSIGNED NOT NULL, -- Faktur terkait dengan satu pesanan
-> invoice_date DATE NOT NULL,
-> due_date DATE NOT NULL,
-> total_amount DECIMAL(15,2) NOT NULL, -- Total tagihan (sama dengan grand_total di orders)
-> paid_amount DECIMAL(15,2) DEFAULT 0,
-> remaining_amount DECIMAL(15,2) NOT NULL,
-> status ENUM('draft', 'sent', 'overdue', 'paid', 'cancelled') DEFAULT 'draft',
-> notes TEXT NULL,
-> created_by BIGINT UNSIGNED NOT NULL,
-> created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
-> updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
->
 -> -- Foreign Keys
-> FOREIGN KEY (order_id) REFERENCES orders(id),
-> FOREIGN KEY (created_by) REFERENCES users(id),
->
 -> -- Indexes
-> INDEX idx_invoice_number (invoice_number),
-> INDEX idx_order_id (order_id),
-> INDEX idx_status (status)
-> ) ENGINE=InnoDB;
Query OK, 0 rows affected (0.265 sec)

MariaDB [axiadb]> show tables;
+------------------------+
| Tables_in_axiadb |
+------------------------+
| assessment_attachments |
| inventory_items |
| inventory_transactions |
| invoices |
| medical_assessments |
| order_items |
| orders |
| patient_attachments |
| patients |
| payments |
| personal_access_tokens |
| product_categories |
| product_components |
| production_orders |
| products |
| suppliers |
| users |
| warehouses |
+------------------------+
18 rows in set (0.001 sec)

MariaDB [axiadb]> -->

Sebagai Senior Project Manager dengan pengalaman 20+ tahun, saya akan memberikan rancangan database yang lebih modern dan terstruktur untuk MVP ERP Klinik Anda. Berikut analisis dan perbaikan dari rancangan sebelumnya:

## 🎯 **ANALISIS RANCANGAN SEBELUMNYA**

**Masalah yang Teridentifikasi:**

1. Terlalu kompleks untuk MVP
2. Banyak field yang belum diperlukan di fase awal
3. Struktur inventory terlalu rumit
4. Kurang fokus pada workflow klinik
5. Audit trail yang berlebihan

## 🗄️ **RANCANGAN DATABASE MVP YANG DISEMPURNAKAN**

### 1. **CORE TABLES (Prioritas MVP)**

```sql
-- Users dengan role yang lebih sederhana
CREATE TABLE users (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'dokter', 'staf_klinik') NOT NULL DEFAULT 'staf_klinik',
    specialization VARCHAR(100) NULL,
    phone VARCHAR(20) NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email_role (email, role)
) ENGINE=InnoDB;

-- Patients dengan data essential saja
CREATE TABLE patients (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    medical_record_number VARCHAR(20) UNIQUE NOT NULL,
    nik VARCHAR(16) NULL,
    name VARCHAR(255) NOT NULL,
    date_of_birth DATE NOT NULL,
    gender ENUM('L', 'P') NOT NULL,
    phone VARCHAR(20) NULL,
    address TEXT NULL,
    emergency_contact VARCHAR(255) NULL,
    insurance_type ENUM('bpjs', 'mandiri', 'asuransi') DEFAULT 'mandiri',
    blood_type ENUM('A', 'B', 'AB', 'O') NULL,
    allergies TEXT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_mrn_name (medical_record_number, name),
    INDEX idx_phone (phone)
) ENGINE=InnoDB;
```

### 2. **CLINICAL WORKFLOW TABLES**

```sql
-- Medical consultations
CREATE TABLE consultations (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    patient_id BIGINT UNSIGNED NOT NULL,
    doctor_id BIGINT UNSIGNED NOT NULL,
    consultation_date DATETIME NOT NULL,
    complaint TEXT NOT NULL,
    diagnosis TEXT NOT NULL,
    treatment_plan TEXT NULL,
    notes TEXT NULL,
    follow_up_date DATE NULL,
    status ENUM('scheduled', 'in_progress', 'completed', 'cancelled') DEFAULT 'scheduled',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (doctor_id) REFERENCES users(id),
    INDEX idx_patient_date (patient_id, consultation_date),
    INDEX idx_doctor_status (doctor_id, status)
) ENGINE=InnoDB;

-- Orthotic/Prosthetic measurements
CREATE TABLE patient_measurements (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    patient_id BIGINT UNSIGNED NOT NULL,
    consultation_id BIGINT UNSIGNED NOT NULL,
    measurement_type ENUM('ortosis', 'protesis', 'alat_bantu') NOT NULL,
    measurement_data JSON NOT NULL, -- { "length": "45cm", "circumference": "30cm" }
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id) ON DELETE CASCADE,
    FOREIGN KEY (consultation_id) REFERENCES consultations(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_patient_type (patient_id, measurement_type)
) ENGINE=InnoDB;
```

### 3. **SERVICE & INVENTORY (Simplified)**

```sql
-- Services/Packages
CREATE TABLE services (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    description TEXT NULL,
    service_type ENUM('konsultasi', 'ortosis', 'protesis', 'terapi', 'alat') NOT NULL,
    price DECIMAL(15,2) NOT NULL,
    duration_days INT NULL, -- Estimated completion time
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code_type (code, service_type)
) ENGINE=InnoDB;

-- Simple inventory for materials
CREATE TABLE inventory_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    item_code VARCHAR(50) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    category ENUM('material', 'component', 'tool') NOT NULL,
    unit VARCHAR(20) NOT NULL,
    current_stock DECIMAL(10,2) DEFAULT 0,
    min_stock DECIMAL(10,2) DEFAULT 5,
    cost_price DECIMAL(10,2) NOT NULL,
    is_active BOOLEAN DEFAULT TRUE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_code_category (item_code, category)
) ENGINE=InnoDB;
```

### 4. **ORDER & PRODUCTION WORKFLOW**

```sql
-- Treatment orders
CREATE TABLE treatment_orders (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    order_number VARCHAR(50) UNIQUE NOT NULL,
    patient_id BIGINT UNSIGNED NOT NULL,
    consultation_id BIGINT UNSIGNED NULL,
    order_date DATE NOT NULL,
    delivery_date DATE NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('draft', 'confirmed', 'production', 'ready', 'delivered', 'cancelled') DEFAULT 'draft',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (patient_id) REFERENCES patients(id),
    FOREIGN KEY (consultation_id) REFERENCES consultations(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_order_number (order_number),
    INDEX idx_status_date (status, order_date)
) ENGINE=InnoDB;

-- Order items
CREATE TABLE order_items (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    service_id BIGINT UNSIGNED NOT NULL,
    quantity INT NOT NULL DEFAULT 1,
    unit_price DECIMAL(15,2) NOT NULL,
    total_price DECIMAL(15,2) NOT NULL,
    specifications JSON NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES treatment_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (service_id) REFERENCES services(id),
    INDEX idx_order_service (order_id, service_id)
) ENGINE=InnoDB;

-- Simple production tracking
CREATE TABLE production_tracking (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    order_id BIGINT UNSIGNED NOT NULL,
    production_stage ENUM('design', 'fabrication', 'assembly', 'fitting', 'completion') NOT NULL,
    notes TEXT NULL,
    completed_by BIGINT UNSIGNED NULL,
    completed_at TIMESTAMP NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES treatment_orders(id) ON DELETE CASCADE,
    FOREIGN KEY (completed_by) REFERENCES users(id),
    INDEX idx_order_stage (order_id, production_stage)
) ENGINE=InnoDB;
```

### 5. **FINANCIAL (Simplified)**

```sql
-- Payments
CREATE TABLE payments (
    id BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    uuid CHAR(36) UNIQUE NOT NULL DEFAULT (UUID()),
    order_id BIGINT UNSIGNED NOT NULL,
    payment_date DATE NOT NULL,
    payment_method ENUM('cash', 'transfer', 'debit_card', 'credit_card') NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    reference_number VARCHAR(100) NULL,
    status ENUM('pending', 'completed', 'failed') DEFAULT 'pending',
    notes TEXT NULL,
    created_by BIGINT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (order_id) REFERENCES treatment_orders(id),
    FOREIGN KEY (created_by) REFERENCES users(id),
    INDEX idx_order_date (order_id, payment_date)
) ENGINE=InnoDB;
```

## 🚀 **MODUL MVP PRIORITAS**

### **Phase 1 (Weeks 1-4)**

1. **Patient Management** - Pendaftaran & data pasien
2. **Consultation Module** - Pemeriksaan dan diagnosis
3. **Service Catalog** - Daftar layanan & harga

### **Phase 2 (Weeks 5-8)**

4. **Order Management** - Pemesanan treatment
5. **Payment Tracking** - Pembayaran sederhana
6. **Basic Reporting** - Laporan harian/bulanan

### **Phase 3 (Weeks 9-12)**

7. **Production Tracking** - Tracking pengerjaan
8. **Inventory Management** - Stok material dasar
9. **Advanced Analytics** - Statistik klinik

## 💡 **REKOMENDASI IMPLEMENTASI**

### **1. Fokus pada Workflow Klinik:**

```
Patient Registration → Consultation → Measurement →
Order Creation → Production → Delivery → Payment
```

### **2. Simplify Inventory:**

-   Mulai dengan tracking material critical saja
-   Gunakan kategori sederhana: material/component/tool
-   Stock alert untuk items penting

### **3. Audit Trail Minimal:**

-   Hanya track `created_by` untuk transaksi penting
-   Gunakan `created_at` dan `updated_at` untuk timeline

### **4. Performance Optimization:**

```sql
-- Essential indexes only
INDEX idx_active_patients (is_active, created_at);
INDEX idx_orders_status (status, created_at);
INDEX idx_payments_status (status, payment_date);
```

## 🎯 **KEUNTUNGAN RANCANGAN BARU**

1. **Lebih Sederhana** - 40% fewer tables
2. **Fokus MVP** - Core workflow klinik saja
3. **Scalable** - Bisa dikembangkan bertahap
4. **Maintainable** - Struktur mudah dipahami
5. **Performance** - Query lebih cepat dengan index minimal

**Rancangan ini siap untuk diimplementasikan dalam 12 minggu dengan tim kecil. Fokus pada delivering value ke pasien dan staff klinik terlebih dahulu, baru kemudian fitur advanced.**

Mau saya breakdown lebih detail untuk phase 1 implementation?
