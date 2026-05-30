# Task Breakdown — Axia Clinic ERP (React SPA + Laravel API) • Shared Hosting Super Debloat

**Sprint:** 0–3 (Fondasi + MVP)
**Lead:** Solo Developer
**Target Deployment:** Shared Hosting cPanel (PHP-FPM, MySQL, tanpa Redis/Docker)
**Frontend:** React 19 + TypeScript + Tailwind CSS v4 (SPA, Vite 8 build)
**Backend:** Laravel 10 (API only, debloated)
**Terakhir diperbarui:** 2026-05-30

---

## Fase 0: Setup Lingkungan Super Debloat & Fondasi Proyek ✅ SELESAI

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-001 | Inisialisasi Laravel 10 minimalis | `composer create-project laravel/laravel:^10.0`, hapus dependensi tidak perlu | ✅ |
| T-002 | Pangkas `config/app.php` | Hapus provider: Broadcast, Auth, Event. Hanya aktifkan: App, Route, Sanctum | ✅ |
| T-003 | Konfigurasi `.env` untuk shared hosting | Cache=file, Queue=database, Session=file, DB=mysql | ✅ |
| T-004 | Setup React SPA project | Vite + Tailwind CSS v4 + React Router v7 + TanStack Query + Zustand + Dexie.js | ✅ |
| T-005 | Konfigurasi Vite untuk output di `public/app` | `base: '/app/'`, build output ke `../public/app`, code splitting | ✅ |
| T-006 | Buat SPA entry point | `routes/web.php` catch-all + inject CSRF + `.htaccess` SPA routing | ✅ |
| T-007 | Setup Sanctum untuk SPA | CORS config, `EnsureFrontendRequestsAreStateful` | ✅ |
| T-008 | Buat script deploy `deploy.sh` | git pull + composer + npm build + artisan cache + migrate | ✅ |
| T-009 | Setup cron job di cPanel | schedule:run, queue:work, mysqldump, cleanup | ✅ |
| T-010 | Deep debloat cleanup | Hapus ~40+ file bloat: template, config lama, provider lama, middleware lama | ✅ |

---

## Fase 1: Core Clinical Workflow ✅ SELESAI

### Backend: Database & Model

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-101 | Migration core tables | `users`, `patients`, `consultations`, `services` + composite index + UUID | ✅ |
| T-102 | Migration `daily_consultation_summaries` | Summary table untuk dashboard | ✅ |
| T-103 | Observer `ConsultationObserver` | Update summary table setiap create/update/delete | ✅ |
| T-104 | Model `User`, `Patient`, `Consultation`, `Service` | Eloquent model dengan relasi | ✅ |
| T-105 | Migration `cache_versions` | Version counter per modul | ✅ |
| T-106 | Buat `CacheHelper` dengan versioning | `remember()`, `incrementVersion()`, `getVersion()` | ✅ |

### Backend: API Controller & Middleware

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-107 | Setup `RoleMiddleware` | Role check di route group | ✅ |
| T-108 | `PatientController` | CRUD, search, pagination, cache versioned | ✅ |
| T-109 | `ConsultationController` | CRUD, today schedule, status filter | ✅ |
| T-110 | `ServiceController` | CRUD + active services | ✅ |
| T-111 | `DashboardController` | Statistik dari summary table | ✅ |
| T-112 | `AuthController` | Login, logout, me, register (admin only) | ✅ |

### Backend: Request Validation

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-113 | `StorePatientRequest` | Validasi lengkap: NIK 16 digit, tanggal lahir, dll | ✅ |
| T-114 | `StoreConsultationRequest` | Validasi status, foreign key | ✅ |

### Frontend: React + TypeScript

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-115 | Setup React Router & layout | Lazy loading setiap halaman, basename `/app` | ✅ |
| T-116 | Auth context & hook | `AuthProvider`, `useAuth`, redirect jika tidak login | ✅ |
| T-117 | Halaman Login | Form login, Sanctum cookie, redirect ke dashboard | ✅ |
| T-118 | Halaman Dashboard | Statistik + grafik tren (Recharts) | ✅ |
| T-119 | Halaman Patients (list + form) | Tabel, search, pagination, import CSV, React Hook Form | ✅ |
| T-120 | Halaman Consultations | Form input, dropdown pasien/dokter, status workflow | ✅ |
| T-121 | Halaman Services | CRUD dengan tabel dan form | ✅ |

### PWA & Offline Sync

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-122 | Setup Service Worker & manifest | PWA, cache aset statis | ✅ |
| T-123 | Integrasi Dexie.js | Schema IndexedDB untuk offline data | ✅ |
| T-124 | Hook `useOfflineSync` | Cek koneksi, simpan offline, sync batch saat online | ✅ |
| T-125 | Endpoint `POST /api/sync/batch` | Terima array transaksi, validasi, insert | ✅ |
| T-126 | UI indicator status sync | Badge "Offline - N transaksi menunggu" di header | ✅ |

---

## Fase 2: Order, Payment, Production ✅ SELESAI

### Backend: Migration & Model

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-201 | Migration order/payment/production tables | `treatment_orders`, `order_items`, `payments`, `production_tracking` | ✅ |
| T-202 | Migration `daily_revenue_summaries` + Observer | Update total pendapatan harian | ✅ |
| T-203 | Model & hubungan | Order, Payment, ProductionTracking dengan relasi | ✅ |

### Backend: Controller

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-204 | `OrderController` | CRUD, nomor order otomatis, status workflow | ✅ |
| T-205 | `PaymentController` | Input pembayaran, verifikasi, update status | ✅ |
| T-206 | `ProductionController` | Update stage produksi per order | ✅ |
| T-207 | Job `GenerateReport` (batch) | `fputcsv()` streaming, simpan di `export_jobs` | ✅ |
| T-208 | Endpoint export status & download | `GET /api/exports/{uuid}/download` | ✅ |

### Frontend

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-209 | Halaman Orders | List, buat order, detail dengan status tracking | ✅ |
| T-210 | Halaman Pembayaran | Form input, list pembayaran | ✅ |
| T-211 | Halaman Tracking Produksi | Tampilan per tahap, tombol update status | ✅ |
| T-212 | Halaman Laporan | Pilih jenis + rentang tanggal, export CSV, riwayat download | ✅ |

---

## Fase 3: Inventory, Backup, Audit ✅ SELESAI

### Backend

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-301 | Migration `inventory_items`, `inventory_transactions` | 3 kategori, min_stok, riwayat transaksi | ✅ |
| T-302 | `InventoryController` | CRUD, stok masuk/keluar/adjustment, cache versioned | ✅ |
| T-303 | Alert stok minimum | Response flag saat stok < min, Dashboard tampilkan peringatan | ✅ |
| T-304 | Backup restore script | `restore.sh` + dokumentasi | ✅ |
| T-305 | Audit log observer | `AuditLog` model + observer di model utama | ✅ |
| T-306 | Endpoint bulk import pasien | `POST /api/patients/import` (CSV streaming) | ✅ |

### Frontend

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-307 | Halaman Inventory | Tabel barang, form transaksi stok, ringkasan statistik | ✅ |
| T-308 | Dashboard Analytics Lanjutan | Grafik Recharts: tren konsultasi, pendapatan, status order, pipeline produksi | ✅ |
| T-309 | Halaman Audit Log (admin) | Tabel log, filter model + tanggal, expandable detail perubahan | ✅ |
| T-310 | Halaman import pasien (admin) | Drag-drop upload CSV, template download, hasil import | ✅ |

### Uji & Deployment

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-311 | Uji offline sync & batch | Simulasikan offline → input → online → verifikasi sync | ✅ |
| T-312 | Uji export laporan besar | 10k baris, memory < 5MB, tidak timeout | ✅ |
| T-313 | Uji backup & restore | Jalankan restore, verifikasi database normal | ✅ |
| T-314 | Deploy production | Jalankan `deploy.sh`, cek semua route, cache, SPA routing | ✅ |

---

## Task Tambahan: Perbaikan Hasil Review (2026-05-30)

### Backend — Kritis

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-401 | Fix SyncController: tambah validasi + role check | Tambah FormRequest, cek role admin/staf_klinik sebelum proses sync | ☐ |
| T-402 | Fix BackupController `restore()`: tambah sanitasi path | Gunakan `basename()`, cek ekstensi `.sql.gz`, konsisten dengan `download()` dan `destroy()` | ☐ |

### Backend — Sedang

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-403 | Fix InventoryController: tambah cache bump | Tambah `CacheHelper::bumpVersion('inventory')` di `store()`, `update()`, `destroy()`, `adjustStock()` | ☐ |
| T-404 | Restringi CORS | Ganti `['*']` dengan metode/header yang dipakai: GET, POST, PUT, PATCH, DELETE, OPTIONS; Content-Type, X-Requested-With, X-XSRF-TOKEN | ☐ |
| T-405 | Fix BackupController: hindari password di CLI | Pastikan `.my.cnf` wajib ada, tolak backup jika tidak tersedia | ☐ |
| T-406 | Fix RoleMiddleware: return JSON 401 | Ganti `redirect()->route('login')` dengan `response()->json(['message' => 'Unauthorized'], 401)` | ☐ |
| T-407 | Validasi parameter `type` di AuditController | Filter terhadap keys yang ada di `$modelMap`, tolak sisanya | ☐ |

### Backend — Ringan

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-408 | Tambah login attempt logging | Log failed login attempts dengan IP dan email | ☐ |
| T-409 | Wire up `config/permissions.php` atau hapus | Gunakan config di middleware/controller, atau hapus dead code | ☐ |
| T-410 | Konsistensi Form Request | Buat UpdatePatientRequest, UpdateConsultationRequest, dll | ☐ |
| T-411 | Set `APP_DEBUG=false` di `.env.example` | Cegah deploy produksi dengan debug mode aktif | ☐ |

### Frontend — Kritis

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-412 | Tambah fokus trap di Modal | Tambah `role="dialog"`, `aria-modal="true"`, fokus trap | ☐ |
| T-413 | Tambah route-level role guards | Buat `ProtectedRoute` component yang cek role sebelum render | ☐ |

### Frontend — Sedang

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-414 | Konsolidasi dark class toggle | Satukan di satu tempat (appStore atau useTheme), hapus duplikasi | ☐ |
| T-415 | Fix `useImportPatients`: pakai `api` client | Ganti raw `fetch` dengan `api.post()`, lempar `Error` object | ☐ |
| T-416 | Fix silent auth failure | Bedakan 401 (expected) dari 500 (server error) di AuthContext | ☐ |
| T-417 | Fix logout error handling | Tambah try-catch, reset `loggingOut` state on error | ☐ |

### Frontend — Ringan

| ID | Task | Detail | Status |
|----|------|--------|--------|
| T-418 | Tambah API timeout | Gunakan `AbortController` dengan timeout configurable | ☐ |
| T-419 | Split `recharts` ke chunk terpisah | Tambah `recharts` ke `manualChunks` di vite.config.ts | ☐ |
| T-420 | Hapus `key={useLocation().pathname}` | Ganti dengan CSS transition tanpa remount | ☐ |
| T-421 | Toast auto-dismiss cleanup | Simpan timeout ID, cleanup saat unmount | ☐ |

---

## Panduan Pengembangan

### Urutan Development (BENAR)
1. **Backend dulu**: Migration → Model → Controller (JSON) → routes/api.php
2. **Frontend kedua**: TypeScript interface → TanStack Query hook → Page component → Route
3. **Build & deploy**: `npm run build` di `frontend/` → `./deploy.sh` di server

### Key Patterns
- **Client state**: Zustand untuk UI state (sidebar, theme, modal). React Context hanya untuk auth.
- **Data fetching**: TanStack Query (`useQuery`/`useMutation`). Jangan buat custom fetch hook manual.
- **Cache versioning**: Increment version di `cache_versions` setelah write. Key otomatis berubah.
- **React bundle**: `React.lazy` untuk setiap halaman. Bundle < 150 KB per chunk.
- **API response**: Semua controller return JSON. Tidak ada `view()` atau `redirect()`.
- **Error handling**: 401 global via `apiFetch`. Error lain per komponen via TanStack Query `onError`.
- **Offline sync**: Dexie.js → `useOfflineSync` → `/api/sync/batch` dengan rate limiter.
- **Queue**: Cron tiap 5 menit, export job diproses batch.
