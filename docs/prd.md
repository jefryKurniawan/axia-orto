# PRD: Axia Clinic ERP (MVP) — React SPA + Laravel API • Shared Hosting Super Debloat

**Versi:** 3.1.0
**Tanggal:** 2026-05-30
**Status:** Produksi (dengan catatan perbaikan)

---

## 1. Ringkasan Eksekutif

Axia Clinic membutuhkan sistem ERP klinik ortotik-prostetik dengan antarmuka modern dan interaktif. Untuk mencapai itu, dipilih **React + TypeScript** (SPA) yang berkomunikasi dengan **Laravel 10 REST API**. Seluruh sistem berjalan di **shared hosting murah** (cPanel) tanpa Docker, Redis, atau FrankenPHP. Backend dipangkas habis (service provider minimal), cache menggunakan file system dengan versioning, queue database batch, dan export native. Frontend React di-build menjadi static files dan diletakkan di folder `public/app`, sehingga dapat disajikan langsung oleh web server tanpa memerlukan Node.js di server.

**Status saat ini:** Seluruh 3 fase pengembangan sudah selesai. Sistem sudah berjalan dan digunakan. Review kode fullstack dilakukan pada 2026-05-30 dengan skor 7/10 — siap produksi dengan beberapa perbaikan yang perlu dilakukan.

---

## 2. Masalah Utama & Batasan Lingkungan

- **Shared hosting tidak mendukung Redis, Docker, FrankenPHP** — hanya PHP-FPM, MySQL, file storage.
- **Memori & CPU terbatas** — frontend React tidak boleh berukuran besar, backend tidak boleh boot berulang-ulang.
- **Jumlah file dalam satu folder dibatasi** — file report & backup disebar per tanggal.
- **Koneksi klinik tidak selalu stabil** — perlu mode offline untuk input data.
- **Data akan terus bertambah** — query report dioptimalkan dengan summary table.

---

## 3. Target Pengguna

| Peran | Deskripsi |
|-------|-----------|
| Admin Klinik | Akses penuh ke semua modul, kelola pengguna, audit log |
| Dokter / Terapis | Konsultasi, diagnosis, rencana perawatan |
| Staf Klinik (Front Office) | Pendaftaran pasien, order, pembayaran |
| Teknisi (Produksi) | Tracking produksi, update status pengerjaan |

---

## 4. Ruang Lingkup (MVP 3 Fase)

### Fase 1 — Core Clinical Workflow ✅ SELESAI
- Manajemen pasien (CRUD, import CSV, rekam medis)
- Konsultasi (jadwal, anamnesis, diagnosis, rencana perawatan)
- Katalog layanan (5 tipe: konsultasi, ortosis, protesis, terapi, alat)
- Dashboard ringkasan harian + grafik tren
- Autentikasi Sanctum (cookie-based SPA)
- PWA offline sync (Dexie.js + IndexedDB)
- Cache versioning (tabel `cache_versions`)

### Fase 2 — Order, Payment, Production ✅ SELESAI
- Order perawatan (multi-item, status workflow)
- Pembayaran (4 metode, status tracking)
- Tracking produksi (tahapan per order, penugasan teknisi)
- Export CSV via queue batch (pendapatan, pasien, order, pembayaran)
- Summary tables (daily_consultation_summaries, daily_revenue_summaries)

### Fase 3 — Inventory, Backup, Audit ✅ SELESAI
- Inventaris (3 kategori, stok masuk/keluar/adjustment, riwayat transaksi)
- Peringatan stok rendah (otomatis di Dashboard)
- Audit log (catatan perubahan data, admin only)
- Backup otomatis via cron (mysqldump, retensi 7 hari)
- Register pengguna baru (admin only)

---

## 5. Technical Stack Super Debloat

| Komponen | Pilihan | Keterangan |
|----------|---------|------------|
| Runtime | PHP 8.2/8.3 + PHP-FPM | OPcache wajib aktif |
| Backend | Laravel 10 (API-only) | Pruned providers, JSON response only |
| Database | MySQL 8.0 (`axiadb`) | InnoDB, composite indexes, summary tables |
| Cache | `file` driver + versioning key | `cache_versions` table, key: `{module}.{action}.v{version}` |
| Queue | `database` driver | Batch processing via cron tiap 5 menit |
| Frontend | React 19 + TypeScript + Tailwind CSS v4 | SPA, Vite 8 build → `public/app/` |
| Icons | lucide-react | Lightweight, tree-shakeable |
| Auth State | React Context | User login/logout, role |
| Client State | Zustand | UI state (sidebar, theme, modal), offline status |
| Routing | React Router v7 | Lazy loading setiap halaman (code splitting) |
| HTTP | fetch (native) | TanStack Query + fetch sudah cukup |
| Data Fetching | TanStack Query (React Query) | Wajib untuk semua data fetching |
| Forms | React Hook Form | Client-side validation |
| Offline | Dexie.js (IndexedDB) | Batch sync saat online |
| Export | Native PHP `fputcsv()` | Streaming ke file, RAM hemat |
| Auth | Laravel Sanctum (cookie-based SPA) | `EnsureFrontendRequestsAreStateful` |
| Build | Vite 8 (frontend), Composer (backend) | Output React ke `public/app/` |

**Yang TIDAK dipakai:**
Redis, Docker, FrankenPHP, Inertia.js, jQuery, Alpine.js, Livewire, Blade (selain entry point), axios, Redux, Material UI, Ant Design, sparte/simple-excel, Create React App.

---

## 6. Arsitektur Sistem

```
[Browser] ──── static files (React SPA) ────> [Shared Hosting: Apache]
                  │
                  │ ① /app/patients → .htaccess rewrite → Laravel
                  │ ② Laravel inject CSRF → serve index.html
                  │ ③ React Router handle /patients
                  │
                  │ API calls: /api/*
                  ▼
         [Laravel API: /api/*] → JSON response only
                  │
                  ▼
            [MySQL Database]
```

### SPA Routing Chain (Shared Hosting)

```
Browser: /app/patients
  ↓
① public/.htaccess → file tidak ada → rewrite ke index.php (Laravel)
  ↓
② routes/web.php catch-all → baca public/app/index.html → inject <meta name="csrf-token">
  ↓
③ Serve HTML ke browser → React SPA load
  ↓
④ React Router v7 handle /patients di client
```

---

## 7. Database Schema

### Core Tables

| Model | Table | Fitur Utama |
|-------|-------|-------------|
| User | `users` | admin, dokter, staf_klinik, teknisi |
| Patient | `patients` | UUID, soft deletes, consent_given |
| Consultation | `consultations` | UUID, soft deletes, status workflow |
| Service | `services` | UUID, soft deletes, 5 tipe layanan |
| TreatmentOrder | `treatment_orders` | Order dari konsultasi, status workflow |
| OrderItem | `order_items` | Detail item order |
| Payment | `payments` | Pembayaran & invoice, 4 metode |
| ProductionTracking | `production_trackings` | Alur produksi ortosis per tahap |
| InventoryItem | `inventory_items` | Stok material, 3 kategori |
| InventoryTransaction | `inventory_transactions` | Riwayat masuk/keluar/adjustment |

### Summary & System Tables

| Table | Fungsi |
|-------|--------|
| `daily_consultation_summaries` | Ringkasan konsultasi harian |
| `daily_revenue_summaries` | Ringkasan pendapatan harian |
| `cache_versions` | Version counter per modul |
| `audit_logs` | Jejak perubahan data |
| `export_jobs` | Status export CSV async |

---

## 8. API Endpoints

### Autentikasi
- `POST /api/login` — Masuk
- `POST /api/logout` — Keluar
- `GET /api/me` — Data pengguna saat ini
- `POST /api/register` — Buat akun (admin only)

### Pasien
- `GET /api/patients` — Daftar pasien (search, pagination)
- `POST /api/patients` — Tambah pasien
- `GET /api/patients/{uuid}` — Detail pasien
- `PUT /api/patients/{uuid}` — Edit pasien
- `DELETE /api/patients/{uuid}` — Hapus pasien
- `POST /api/patients/import` — Import CSV

### Konsultasi
- `GET /api/consultations` — Daftar konsultasi (search, filter status)
- `POST /api/consultations` — Tambah konsultasi
- `GET /api/consultations/{uuid}` — Detail konsultasi
- `PUT /api/consultations/{uuid}` — Edit konsultasi
- `DELETE /api/consultations/{uuid}` — Hapus konsultasi

### Layanan
- `GET /api/services` — Daftar layanan
- `GET /api/services/active` — Layanan aktif saja
- `POST /api/services` — Tambah layanan
- `PUT /api/services/{uuid}` — Edit layanan
- `DELETE /api/services/{uuid}` — Hapus layanan

### Order
- `GET /api/orders` — Daftar order
- `POST /api/orders` — Buat order
- `GET /api/orders/{uuid}` — Detail order
- `PUT /api/orders/{uuid}` — Edit order
- `PATCH /api/orders/{uuid}/status` — Ubah status order
- `DELETE /api/orders/{uuid}` — Hapus order

### Pembayaran
- `GET /api/payments` — Daftar pembayaran
- `POST /api/payments` — Catat pembayaran
- `GET /api/payments/{uuid}` — Detail pembayaran
- `PUT /api/payments/{uuid}` — Edit pembayaran
- `DELETE /api/payments/{uuid}` — Hapus pembayaran

### Produksi
- `GET /api/production` — Daftar tracking produksi
- `POST /api/production` — Tambah tracking
- `GET /api/production/{uuid}` — Detail tracking
- `PUT /api/production/{uuid}` — Edit tracking
- `DELETE /api/production/{uuid}` — Hapus tracking

### Inventaris
- `GET /api/inventory` — Daftar item inventaris
- `POST /api/inventory` — Tambah item
- `GET /api/inventory/{uuid}` — Detail item + riwayat transaksi
- `PUT /api/inventory/{uuid}` — Edit item
- `DELETE /api/inventory/{uuid}` — Hapus item
- `POST /api/inventory/{uuid}/adjust` — Penyesuaian stok

### Laporan & Export
- `GET /api/exports` — Riwayat export
- `POST /api/exports` — Buat export baru
- `GET /api/exports/{uuid}/download` — Download file CSV

### Dashboard & Audit
- `GET /api/dashboard` — Statistik ringkasan
- `GET /api/audit-logs` — Catatan perubahan data (admin only)

### Sync (Offline)
- `POST /api/sync/batch` — Sinkronisasi data offline

---

## 9. Review Keamanan & Kode (2026-05-30)

### Skor: 7/10 — Siap produksi dengan beberapa perbaikan

### Temuan Kritis (Backend)
1. **SyncController tanpa validasi dan pengecekan peran** — Endpoint `/api/sync/batch` menerima data mentah tanpa validasi dan tanpa pengecekan role. Semua pengguna yang sudah login bisa membuat/mengubah/menghapus data melalui endpoint ini.
2. **BackupController `restore()` tanpa sanitasi path** — Parameter `filename` tidak menggunakan `basename()` dan tidak mengecek ekstensi `.sql.gz`.

### Temuan Sedang (Backend)
3. InventoryController tidak memperbarui cache setelah mutasi.
4. CORS terlalu longgar (`allowed_methods: ['*']`, `allowed_headers: ['*']`).
5. BackupController membocorkan password MySQL via CLI saat `.my.cnf` tidak tersedia.
6. RoleMiddleware mengembalikan redirect HTML alih-alih JSON 401.
7. AuditController tidak memvalidasi parameter `type` terhadap model map.

### Temuan Ringan (Backend)
8. Tidak ada logging percobaan login gagal atau mekanisme lockout.
9. `config/permissions.php` tidak pernah dipakai (dead code).
10. Form Request tidak konsisten (beberapa endpoint pakai inline validation).
11. `.env.example` memiliki `APP_DEBUG=true`.

### Temuan Kritis (Frontend)
12. Tidak ada fokus trap di komponen Modal (pelanggaran aksesibilitas).
13. Tidak ada pengecekan role di routing client-side.

### Temuan Sedang (Frontend)
14. Logika mode gelap di-toggle di 3 tempat berbeda.
15. `useImportPatients` melewati API client terpusat.
16. Auth failure yang diam (semua error `/me` diperlakukan sebagai belum login).
17. Logout tanpa penanganan error.

### Temuan Ringan (Frontend)
18. Tidak ada timeout API.
19. `recharts` tidak di-split ke chunk terpisah.
20. `key={useLocation().pathname}` memaksa remount penuh per halaman.
21. Toast auto-dismiss tidak cleanup saat unmount.

### Positif
- Semua route API dilindungi `auth:sanctum`
- Semua query menggunakan parameterized queries (tidak ada SQL injection)
- Perintah shell menggunakan `escapeshellarg()`
- CSRF handling benar untuk SPA auth
- Dependensi minimal, tidak ada bloat
- Export job di-scope ke pengguna yang meminta
- Semua halaman lazy-loaded (code splitting)
- Cache versioning berfungsi dengan baik

---

## 10. Deployment

```bash
# deploy.sh
git pull origin main
composer install --no-dev --optimize-autoloader --no-interaction
cd frontend && npm ci && npm run build && cd ..
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```

### Cron Jobs (cPanel)
```
* * * * * cd /home/user/public_html && php artisan schedule:run
*/5 * * * * cd /home/user/public_html && php artisan queue:work --once --stop-when-empty --queue=reports
0 3 * * * mysqldump --defaults-extra-file=/home/user/.my.cnf axiadb | gzip > /home/user/backups/$(date +\%Y/\%m/\%d)/backup.sql.gz
0 4 * * * find /home/user/backups -mtime +7 -delete
0 4 * * * find /home/user/public_html/storage/app/reports -mtime +30 -delete
```

### Persyaratan Server
- PHP 8.2+ dengan OPcache aktif
- MySQL 8.0+
- `memory_limit = 128M`
- `max_execution_time = 60`
- `.my.cnf` permission 600 untuk backup otomatis

---

## 11. Konvensi

- **Bahasa** — Indonesian untuk komunikasi dan domain. English untuk kode.
- **Commit** — Conventional: `feat:`, `fix:`, `refactor:`, `docs:`
- **Naming** — PascalCase class/component, camelCase method/variable, snake_case database
- **API URL** — `/api/{resource}` (plural), `/api/{resource}/{id}` (single)
- **TypeScript** — Semua file `.tsx`/`.ts`. Interface untuk API response types
- **Error handling** — try-catch di controller → JSON error. React: toast notification

---

*Dokumen ini adalah sumber kebenaran (source of truth) untuk arsitektur dan ruang lingkup Axia Clinic ERP.*
