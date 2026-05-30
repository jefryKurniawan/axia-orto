# Axia Orto Clinic ERP

> Sistem Manajemen Klinik Ortotik & Prostetik — React SPA + Laravel API

## Tentang Project

**Axia Orto** adalah sistem manajemen klinik berbasis web yang dirancang khusus untuk menangani alur kerja di klinik **Ortotik dan Prostetik**.

Dibangun dengan arsitektur **React SPA + Laravel API** yang ringan dan cepat, bisa berjalan di shared hosting murah tanpa Docker, Redis, atau server khusus. Backend hanya mengembalikan JSON, frontend adalah aplikasi React yang di-build menjadi file statis.

## Tech Stack

| Lapisan | Teknologi | Keterangan |
|---|---|---|
| Backend | Laravel 10 (API-only) | Hanya JSON response, tanpa Blade views |
| Frontend | React 19 + TypeScript + Tailwind CSS v4 | SPA, Vite 8 build ke `public/app/` |
| Database | MySQL 8.0 | InnoDB, composite index, summary tables |
| Autentikasi | Laravel Sanctum | Cookie-based SPA auth |
| Data Fetching | TanStack Query | Loading, caching, sync, retry |
| State Management | Zustand + React Context | UI state (Zustand), auth state (Context) |
| Routing | React Router v7 | Lazy loading setiap halaman |
| Offline | Dexie.js (IndexedDB) | Batch sync saat online |
| Ikon | lucide-react | Ikon ringan, tree-shakeable |

## Fitur Utama

- **Dashboard** — Ringkasan harian: konsultasi, pasien, pendapatan, stok rendah, grafik tren
- **Manajemen Pasien** — CRUD pasien, rekam medis, import CSV, data asuransi
- **Konsultasi** — Jadwal, anamnesis, diagnosis, rencana perawatan, kontrol ulang
- **Katalog Layanan** — Daftar layanan ortotik/prostetik dengan harga dan durasi
- **Order Perawatan** — Surat perintah kerja, item breakdown, estimasi pengiriman
- **Pembayaran** — Pencatatan pembayaran, metode bayar, catatan transaksi
- **Tracking Produksi** — Tahapan produksi, penugasan teknisi, catatan pengerjaan
- **Inventaris** — Manajemen stok material, peringatan stok rendah, kategori
- **Laporan** — Export CSV: pendapatan, pasien, order, pembayaran
- **Audit Log** — Jejak perubahan data oleh semua pengguna (admin only)
- **Multi-Role** — Admin, Dokter, Staf Klinik, Teknisi — masing-masing hak akses berbeda
- **Mode Gelap** — Tampilan terang/gelap, tersimpan otomatis
- **Responsif** — Bisa digunakan di HP, tablet, dan laptop

## Screenshots

### Landing Page
![Landing Page](./review/beranda.png)

### Login
![Login](./review/login.png)

### Dashboard
![Dashboard](./review/dashboard.png)

### Manajemen Pasien
![Pasien](./review/pasien.png)

### Konsultasi
![Konsultasi](./review/konsultasi.png)

### Katalog Layanan
![Layanan](./review/layanan.png)

### Order Perawatan
![Order](./review/order.png)

### Pembayaran
![Pembayaran](./review/pembayaran.png)

### Tracking Produksi
![Produksi](./review/produksi.png)

### Inventaris
![Inventaris](./review/inventory.png)

### Laporan
![Laporan](./review/laporan.png)

### Audit Log
![Audit Log](./review/alog.png)

## Review & Status Pengembangan

### Skor Produksi: 7/10 — Siap produksi dengan beberapa perbaikan yang perlu dilakukan

### Temuan Backend

**Kritis (harus diperbaiki sebelum produksi):**
1. **SyncController tanpa validasi dan pengecekan peran** — Endpoint `/api/sync/batch` menerima data mentah tanpa validasi dan tanpa pengecekan role. Semua pengguna yang sudah login (termasuk teknisi) bisa membuat/mengubah/menghapus pasien dan konsultasi melalui endpoint ini, melewati seluruh matrix izin.
2. **BackupController `restore()` tanpa sanitasi path** — Parameter `filename` tidak menggunakan `basename()` dan tidak mengecek ekstensi `.sql.gz`, berbeda dengan `download()` dan `destroy()` yang sudah benar.

**Sedang:**
3. **InventoryController tidak memperbarui cache** — `store()`, `update()`, `destroy()`, dan `adjustStock()` tidak memanggil `CacheHelper::bumpVersion('inventory')`, sehingga data kadaluarsa akan disajikan sampai TTL 300 detik habis.
4. **CORS terlalu longgar** — `allowed_methods: ['*']` dan `allowed_headers: ['*']` di `config/cors.php` seharusnya dibatasi ke metode dan header yang benar-benar dipakai.
5. **BackupController membocorkan password** — Saat `.my.cnf` tidak tersedia, password MySQL dikirim via argumen CLI (`-p`), terlihat oleh pengguna lain di shared hosting melalui `ps aux`.
6. **RoleMiddleware mengembalikan redirect** — Menggunakan `redirect()->route('login')` yang menghasilkan HTTP 302, seharusnya mengembalikan JSON 401 untuk konsistensi API.
7. **AuditController tidak memvalidasi parameter `type`** — Input pengguna langsung dipakai di query jika tidak ada di map, berpotensi menjadi vektor injeksi.

**Ringan:**
8. **Tidak ada logging percobaan login gagal** — Tidak ada mekanisme lockout akun atau pencatatan upaya login yang gagal.
9. **`config/permissions.php` tidak pernah dipakai** — Matrix izin didefinisikan tapi tidak direferensikan oleh controller atau middleware manapun. Semua pengecekan role dilakukan inline.
10. **Form Request tidak konsisten** — Beberapa endpoint menggunakan `$request->validate()` inline alih-alih FormRequest class, berisiko menyebabkan aturan validasi berbeda antara create dan update.
11. **`.env.example` memiliki `APP_DEBUG=true`** — Deploy produksi yang menyalin file ini akan menampilkan debug mode, membocorkan stack trace dan variabel lingkungan.

**Positif:**
- Semua route API dilindungi `auth:sanctum` kecuali login dan health check
- Semua query menggunakan parameterized queries (tidak ada SQL injection)
- Perintah shell menggunakan `escapeshellarg()` pada semua argumen
- CSRF handling benar untuk SPA auth
- Dependensi minimal, tidak ada paket yang tidak perlu
- Export job di-scope ke pengguna yang meminta

### Temuan Frontend

**Kritis:**
1. **Tidak ada fokus trap di Modal** — Pengguna keyboard bisa berinteraksi dengan konten di belakang modal. Pelanggaran aksesibilitas.
2. **Tidak ada pengecekan role di routing** — Semua pengguna yang sudah login bisa mengakses semua route di client. Backend tetap menolak akses, tapi UX buruk (pengguna melihat halaman, lalu dapat error API).

**Sedang:**
3. **Dark class di-toggle di 3 tempat** — Logika mode gelap ada di `appStore.ts`, `useTheme.ts`, dan `App.tsx`. Fragil, seharusnya satu sumber kebenaran.
4. **`useImportPatients` melewati `api` client** — Menggunakan `fetch` langsung, melewatkan error handling terpusat dan melempar objek mentah alih-alih `Error`.
5. **Auth failure yang diam** — Semua error dari `/me` diperlakukan sebagai "belum login", termasuk error server 500.
6. **Logout tanpa penanganan error** — Jika API gagal, status `loggingOut` terjebak selamanya.

**Ringan:**
7. **Tidak ada timeout API** — Request yang menggantung membuat UI tetap dalam status loading tanpa batas.
8. **`recharts` tidak di-split** — Library grafik (~200KB) masuk ke bundle utama, seharusnya di-chunk terpisah.
9. **`key={useLocation().pathname}`** di AppLayout memaksa remount penuh pada setiap perpindahan halaman, menghancurkan state lokal komponen.
10. **Toast auto-dismiss tidak cleanup** — Timeout tetap berjalan meskipun komponen sudah unmount.

### Kesimpulan

Arsitektur dan pola kode secara keseluruhan solid untuk shared hosting. Temuan kritis terutama di SyncController dan aksesibilitas Modal perlu diperbaiki sebelum deploy produksi. Temuan sedang (cache inventory, CORS, konsistensi validasi) sebaiknya diperbaiki dalam sprint berikutnya.

## Arsitektur

```
Browser → static files (React SPA) → Apache
  │
  │ API calls: /api/*
  ▼
Laravel API → MySQL Database
```

- React SPA dimuat dari `/app/index.html`
- Semua permintaan data melalui REST API yang dilindungi Sanctum
- Backend tidak merender HTML — hanya JSON API
- `public/app/.htaccess` menangani routing SPA di shared hosting

## Instalasi

```bash
# 1. Clone repository
git clone https://github.com/jefrykurniawan/axia-orto.git
cd axia-orto

# 2. Install dependencies backend
composer install

# 3. Install dependencies frontend
cd frontend
npm install
npm run build
cd ..

# 4. Setup environment
cp .env.example .env
php artisan key:generate

# 5. Konfigurasi database di .env
# DB_DATABASE=axiadb
# DB_USERNAME=root
# DB_PASSWORD=

# 6. Jalankan migrasi dan data awal
php artisan migrate --seed

# 7. Jalankan server
php artisan serve

# Buka browser: http://localhost:8000/app/
```

## Struktur Project

```
axia-orto/
├── frontend/                # React SPA
│   └── src/
│       ├── pages/           # Halaman-halaman aplikasi
│       ├── components/      # Komponen UI (Button, Card, Table, dll)
│       ├── hooks/           # TanStack Query hooks
│       ├── contexts/        # Auth context
│       ├── stores/          # Zustand stores
│       ├── lib/             # API client, utils
│       └── types/           # TypeScript interfaces
├── app/                     # Laravel API
│   ├── Http/Controllers/    # API controllers (JSON only)
│   ├── Models/              # Eloquent models
│   └── Observers/           # Audit trail, summary update
├── config/permissions.php   # Hak akses per role
├── routes/api.php           # Semua API routes
├── routes/web.php           # SPA catch-all (inject CSRF)
└── public/app/              # React build output
```

## Hak Akses

| Modul | Admin | Dokter | Staf Klinik | Teknisi |
|---|---|---|---|---|
| Pasien | Semua | Lihat | Semua | - |
| Konsultasi | Lihat, Hapus | Semua | Lihat | - |
| Layanan | Semua | Lihat | Lihat | - |
| Order | Semua | Buat, Lihat | Buat, Lihat, Edit | Lihat |
| Pembayaran | Semua | - | Semua | - |
| Produksi | Lihat | Lihat | Lihat | Lihat, Edit |
| Inventaris | Semua | Lihat | Tambah, Lihat | Lihat |
| Laporan | Lihat | Lihat | Lihat | - |
| Audit Log | Lihat | - | - | - |

## Deployment (Shared Hosting)

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
cd frontend && npm ci && npm run build && cd ..
php artisan config:cache
php artisan route:cache
php artisan migrate --force
```

## Panduan Pengguna

Lihat [Panduan-Pengguna.md](./Panduan-Pengguna.md) untuk panduan lengkap penggunaan sistem bagi owner dan admin klinik.

## License

MIT | Laravel 10 + React 19 + MySQL

---

Dibuat oleh **Jefry Kurniawan**
