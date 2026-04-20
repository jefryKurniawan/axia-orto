# 🗂️ **STRUKTUR MENU & SUBMENU SISTEM KLINIK ORTOPEDI (MVP)**

## 📊 **DASHBOARD**

```
📊 Dashboard
├── 📈 Overview Statistik
├── 🔔 Notifikasi & Reminder
├── 📅 Jadwal Hari Ini
└── ⚡ Aktivitas Terbaru
```

## 👥 **MANAJEMEN PASIEN** (Phase 1)

```
👥 Manajemen Pasien
├── 👤 Daftar Pasien
│   ├── 🔍 Pencarian & Filter
│   ├── 📋 Lihat Detail Pasien
│   ├── ✏️ Edit Data Pasien
│   └── 📊 Riwayat Medis
├── ➕ Pendaftaran Pasien Baru
│   ├── 📝 Form Registrasi
│   ├── 🏷️ Generate No. Rekam Medis
│   └── 📸 Upload Dokumen (opsional)
├── 📋 Data Master Pasien
│   ├── 🏷️ Tipe Asuransi
│   ├── 🩸 Golongan Darah
│   └── 📄 Template Data Pasien
└── 📈 Statistik Pasien
    ├── 📊 Demografi
    ├── 🏥 Pola Kunjungan
    └── 📋 Laporan Registrasi
```

## 🩺 **MODUL KONSULTASI** (Phase 1)

```
🩺 Modul Konsultasi
├── 📅 Jadwal Konsultasi
│   ├── 📅 Kalender View
│   ├── 📋 List View
│   ├── ➕ Buat Janji Baru
│   └── 🔄 Update Status
├── 🏥 Pemeriksaan Pasien
│   ├── 📝 Anamnesis & Keluhan
│   ├── 🔍 Diagnosis
│   ├── 💊 Rencana Perawatan
│   └── 📋 Template Diagnosis
├── 📐 Pengukuran Ortopedi
│   ├── 📏 Ortosis
│   ├── 🦿 Protesis
│   ├── 🩼 Alat Bantu
│   └── 💾 Simpan Data Pengukuran
├── 📋 Riwayat Konsultasi
│   ├── 🔍 Pencarian Berdasarkan Pasien
│   ├── 📊 Ringkasan Medis
│   └── 📄 Export Riwayat
└── ⏰ Follow-up & Reminder
    ├── 🔔 Jadwal Follow-up
    ├── 📧 Notifikasi Pasien
    └── 📋 Monitoring Progress
```

## 💰 **LAYANAN & HARGA** (Phase 1)

```
💰 Layanan & Harga
├── 📋 Katalog Layanan
│   ├── 🏷️ Daftar Semua Layanan
│   ├── 🔍 Filter Berdasarkan Tipe
│   ├── ✏️ Edit Harga & Deskripsi
│   └── 🔄 Status Aktif/Non-Aktif
├── 🏷️ Manajemen Tipe Layanan
│   ├── 🩺 Konsultasi
│   ├── 📐 Ortosis
│   ├── 🦿 Protesis
│   ├── 🏃 Terapi
│   └── 🩼 Alat Bantu
├── 💵 Struktur Harga
│   ├── 💰 Update Harga
│   ├── 📊 History Perubahan Harga
│   └── 🏷️ Harga Khusus (opsional)
└── 📄 Template Layanan
    ├── 📝 Deskripsi Standar
    ├── ⏱️ Durasi Pengerjaan
    └── 📋 Spesifikasi Teknis
```

## 📦 **MANAJEMEN PESANAN** (Phase 2)

```
📦 Manajemen Pesanan
├── 🆕 Pesanan Baru
│   ├── 👥 Pilih Pasien
│   ├── 💰 Pilih Layanan
│   ├── 📝 Spesifikasi Pesanan
│   └── 🏷️ Generate No. Pesanan
├── 📋 Semua Pesanan
│   ├── 🔍 Filter Berdasarkan Status
│   ├── 📊 Dashboard Status Pesanan
│   ├── ✏️ Edit Pesanan
│   └── 📄 Detail Pesanan Lengkap
├── 🚚 Tracking Produksi
│   ├── 🎨 Design
│   ├── 🔨 Fabrication
│   ├── 🔧 Assembly
│   ├── 📏 Fitting
│   └── ✅ Completion
├── 📦 Delivery & Pengiriman
│   ├── 🗓️ Jadwal Delivery
│   ├── ✅ Konfirmasi Penerimaan
│   └── 📋 Berita Acara Serah Terima
└── 📊 Monitoring Pesanan
    ├── ⚠️ Pesanan Tertunda
    ├── 📈 Performance Timeline
    └── 📊 Analytics Pesanan
```

## 💳 **MANAJEMEN PEMBAYARAN** (Phase 2)

```
💳 Manajemen Pembayaran
├── 💰 Input Pembayaran
│   ├── 👥 Pilih Pesanan
│   ├── 💵 Input Amount
│   ├── 🏦 Metode Pembayaran
│   └── 📧 Generate Invoice
├── 📋 Riwayat Pembayaran
│   ├── 🔍 Filter Berdasarkan Periode
│   ├── 📊 Status Pembayaran
│   └── 📄 Detail Transaksi
├── 🧾 Invoice Management
│   ├── 📄 Generate Invoice
│   ├── ✏️ Edit Invoice
│   └── 🖨️ Cetak Invoice
└── 💰 Laporan Keuangan
    ├── 📊 Cash Flow Harian
    ├── 📈 Pendapatan Bulanan
    └── 📋 Rekonsiliasi Bank
```

## 📦 **MANAJEMEN INVENTORI** (Phase 3)

```
📦 Manajemen Inventori
├── 📋 Daftar Barang
│   ├── 🏷️ Material
│   ├── 🔩 Component
│   ├── 🛠️ Tool
│   └── 🔄 Status Aktif/Non-Aktif
├── 📊 Stok Management
│   ├── ➕ Stok Masuk
│   ├── ➖ Stok Keluar
│   ├── 📊 Stock Opname
│   └── ⚠️ Alert Stok Minimum
├── 💰 Manajemen Harga Pokok
│   ├── 💵 Update Harga Beli
│   ├── 📊 History Perubahan Harga
│   └── 📈 Analisis Biaya
└── 📈 Laporan Inventori
    ├── 📊 Movement Report
    ├️ ⚠️ Low Stock Report
    └️ 💰 Valuation Report
```

## 📊 **LAPORAN & ANALYTICS** (Phase 2 & 3)

```
📊 Laporan & Analytics
├── 📈 Laporan Klinik
│   ├── 📊 Kunjungan Pasien
│   ├── 🩺 Aktivitas Dokter
│   ├── 💰 Pendapatan Layanan
│   └️ 📋 Efektivitas Treatment
├── 💼 Laporan Bisnis
│   ├️ 📈 Financial Summary
│   ├️ 📦 Performance Pesanan
│   ├️ 👥 Statistik Pasien
│   └️ 📊 Inventory Turnover
├️ 🎯 Custom Reports
│   ├️ 📅 Laporan Periode Tertentu
│   ├️ 👥 Filter Berdasarkan Dokter
│   └️ 🏷️ Filter Berdasarkan Layanan
└️ 📤 Export & Share
    ├️ 📄 Export ke PDF
    ├️ 📊 Export ke Excel
    └️ 📧 Email Report
```

## ⚙️ **PENGATURAN SISTEM** (Phase 3)

```
⚙️ Pengaturan Sistem
├── 👥 Manajemen User
│   ├── 👤 Daftar User
│   ├── ➕ Tambah User Baru
│   ├️ 🎭 Role & Permission
│   └️ 🔄 Status Aktif/Non-Aktif
├️ 🏢 Setup Klinik
│   ├️ 🏷️ Informasi Klinik
│   ├️ 📧 Template Email/SMS
│   ├️ 🔔 Setting Notifikasi
│   └️ 📊 Preference Laporan
├️ 🔧 Konfigurasi Sistem
│   ├️ 🏷️ Kode & Numbering
│   ├️ 💰 Mata Uang & Format
│   ├️ 📅 Timezone & Bahasa
│   └️ 🔒 Backup & Restore
└️ 📊 System Monitoring
    ├️ 🔍 Activity Log
    ├️ ⚠️ System Health
    └️ 📈 Performance Metrics
```

## 🔐 **AUTH & PROFILE**

```
🔐 Auth & Profile
├── 👤 User Profile
│   ├️ ✏️ Edit Profile
│   ├️ 🔒 Change Password
│   ├️ 📧 Notification Settings
│   └️ 🎨 Theme Preference
├️ 🔐 Authentication
│   ├️ 🔑 Login
│   ├️ 📱 Forgot Password
│   ├️ ✏️ Reset Password
│   └️ 🚪 Logout
└️ 🛡️ Security
    ├️ 📱 Two-Factor Auth
    ├️ 🔍 Login History
    └️ ⚠️ Security Alerts
```

## 🎯 **WORKFLOW INTEGRATION**

```
🔄 Clinical Workflow
├── 1️⃣ Pendaftaran Pasien
│   → 2️⃣ Konsultasi & Diagnosis
│   → 3️⃣ Pengukuran & Assessment
│   → 4️⃣ Pemesanan Treatment
│   → 5️⃣ Produksi & Tracking
│   → 6️⃣ Delivery & Fitting
│   → 7️⃣ Pembayaran
│   → 8️⃣ Follow-up
└── 📊 Monitoring & Evaluation
```

## 📱 **MOBILE & QUICK ACCESS**

```
📱 Quick Actions
├── ➕ Pasien Baru (Quick Register)
├── 🩺 Konsultasi Cepat
├️ 💰 Pembayaran Instant
├️ 📦 Cek Status Pesanan
└️ 🔍 Cari Pasien
```

Struktur ini mengikuti:

-   **Phase 1**: Dashboard, Pasien, Konsultasi, Layanan
-   **Phase 2**: Pesanan, Pembayaran, Laporan Dasar
-   **Phase 3**: Inventori, Analytics Lanjutan, Pengaturan

Setiap menu dan submenu siap untuk diimplementasikan dalam view Laravel dengan Tailwind CSS dan jQuery sesuai kebutuhan workflow klinik ortopedi.

# 🎯 **PENJELASAN DETAIL CRUD UNTUK SETIAP MENU**

Berdasarkan struktur menu yang sudah dibuat, berikut penjelasan **CRUD (Create, Read, Update, Delete)** untuk setiap modul:

## 👥 **MANAJEMEN PASIEN** (CRUD Lengkap)

### **1. Daftar Pasien**

```
📋 Daftar Pasien
├── 🔍 **READ** - Menampilkan list semua pasien
├── 👁️ **READ** - View detail pasien (show)
├── ✏️ **UPDATE** - Edit data pasien
├── 🗑️ **DELETE** - Hapus data pasien (soft delete)
├── 📊 **READ** - Filter & search
└── 📄 **READ** - Export data
```

### **2. Pendaftaran Pasien Baru**

```
➕ Pendaftaran Pasien Baru
├── 📝 **CREATE** - Form input data baru
├── 💾 **CREATE** - Simpan data ke database
├── 🏷️ **CREATE** - Generate nomor rekam medis otomatis
└── 📸 **CREATE/UPLOAD** - Upload dokumen pendukung
```

### **3. Data Master Pasien**

```
📚 Data Master Pasien
├── 🏷️ **CRUD** - Tipe asuransi (bpjs, mandiri, asuransi)
├── 🩸 **CRUD** - Data master golongan darah
├── 📄 **CRUD** - Template form pendaftaran
└── ⚙️ **CRUD** - Konfigurasi field wajib/opsional
```

### **4. Statistik Pasien**

```
📈 Statistik Pasien
├── 📊 **READ** - Menampilkan grafik demografi
├── 📋 **READ** - Laporan jumlah pasien per periode
└── 📄 **READ** - Export statistik (hanya baca, tidak ada create/edit/delete)
```

## 🩺 **MODUL KONSULTASI** (CRUD Lengkap)

### **1. Jadwal Konsultasi**

```
📅 Jadwal Konsultasi
├── ➕ **CREATE** - Buat janji konsultasi baru
├── 👁️ **READ** - Lihat detail jadwal
├── ✏️ **UPDATE** - Reschedule/jadwal ulang
├── 🗑️ **DELETE** - Batalkan konsultasi
└── 🔄 **UPDATE** - Update status (scheduled → in_progress → completed)
```

### **2. Pemeriksaan Pasien**

```
🏥 Pemeriksaan Pasien
├── 📝 **CREATE** - Input data anamnesis & keluhan
├── 🔍 **CREATE** - Input diagnosis
├── 💊 **CREATE** - Input rencana perawatan
├── ✏️ **UPDATE** - Edit data pemeriksaan
├── 📋 **READ** - Lihat riwayat pemeriksaan
└── 📄 **READ** - Cetak hasil pemeriksaan
```

### **3. Pengukuran Ortopedi**

```
📐 Pengukuran Ortopedi
├── 📏 **CREATE** - Input data pengukuran ortosis/protesis
├── 👁️ **READ** - Lihat history pengukuran
├── ✏️ **UPDATE** - Edit data pengukuran
├── 🗑️ **DELETE** - Hapus data pengukuran
└── 💾 **CREATE** - Simpan spesifikasi teknis (JSON)
```

## 💰 **LAYANAN & HARGA** (CRUD Lengkap)

### **1. Katalog Layanan**

```
📋 Katalog Layanan
├── ➕ **CREATE** - Tambah layanan baru
├── 👁️ **READ** - Lihat detail layanan
├── ✏️ **UPDATE** - Edit data layanan
├── 🗑️ **DELETE** - Hapus layanan (soft delete)
├── 🔄 **UPDATE** - Aktif/non-aktif layanan
└── 💵 **UPDATE** - Update harga
```

### **2. Struktur Harga**

```
💵 Struktur Harga
├── ✏️ **UPDATE** - Update harga layanan
├── 📊 **READ** - History perubahan harga
├── 💰 **CREATE** - Set harga khusus (promo)
└── 🗑️ **DELETE** - Hapus harga khusus
```

## 📦 **MANAJEMEN PESANAN** (CRUD Lengkap)

### **1. Pesanan Baru**

```
🆕 Pesanan Baru
├── ➕ **CREATE** - Buat pesanan baru
├── 👥 **READ** - Pilih dari data pasien
├── 💰 **READ** - Pilih dari katalog layanan
├── 📝 **CREATE** - Input spesifikasi pesanan
└── 🏷️ **CREATE** - Generate nomor pesanan otomatis
```

### **2. Semua Pesanan**

```
📋 Semua Pesanan
├── 👁️ **READ** - Lihat detail pesanan lengkap
├── ✏️ **UPDATE** - Edit data pesanan
├── 🗑️ **DELETE** - Batalkan pesanan
├── 🔄 **UPDATE** - Update status pesanan
└── 📄 **READ** - Cetak invoice
```

### **3. Tracking Produksi**

```
🚚 Tracking Produksi
├── ➕ **CREATE** - Input progress produksi
├── 👁️ **READ** - Monitor status produksi
├── ✏️ **UPDATE** - Update progress
├── ✅ **UPDATE** - Tandai stage selesai
└── 📊 **READ** - Timeline produksi
```

## 💳 **MANAJEMEN PEMBAYARAN** (CRUD Lengkap)

### **1. Input Pembayaran**

```
💰 Input Pembayaran
├── ➕ **CREATE** - Input data pembayaran
├── 💵 **CREATE** - Konfirmasi pembayaran
├── 🏦 **CREATE** - Pilih metode pembayaran
└── 🧾 **CREATE** - Generate invoice otomatis
```

### **2. Riwayat Pembayaran**

```
📋 Riwayat Pembayaran
├── 👁️ **READ** - Lihat detail transaksi
├── ✏️ **UPDATE** - Edit data pembayaran (koreksi)
├── 🗑️ **DELETE** - Hapus transaksi (void)
└── 🔄 **UPDATE** - Update status pembayaran
```

## 📦 **MANAJEMEN INVENTORI** (CRUD Lengkap - Phase 3)

### **1. Daftar Barang**

```
📋 Daftar Barang
├── ➕ **CREATE** - Tambah item inventory
├── 👁️ **READ** - Lihat detail barang
├── ✏️ **UPDATE** - Edit data barang
├── 🗑️ **DELETE** - Hapus barang (soft delete)
└── 🔄 **UPDATE** - Aktif/non-aktif barang
```

### **2. Stok Management**

```
📊 Stok Management
├── ➕ **CREATE** - Stok masuk (pembelian)
├── ➖ **CREATE** - Stok keluar (pemakaian)
├── ✏️ **UPDATE** - Koreksi stok
├── 📊 **READ** - Monitoring level stok
└️ ⚠️ **READ** - Alert stok minimum
```

## 📊 **LAPORAN & ANALYTICS** (READ Only)

```
📊 Laporan & Analytics
├── 📈 **READ** - Semua laporan hanya untuk dilihat
├── 📄 **READ** - Export data (PDF, Excel)
├️ 📧 **READ** - Share report (email)
└️ 🔍 **READ** - Filter & search data
```

## ⚙️ **PENGATURAN SISTEM** (CRUD Lengkap)

### **1. Manajemen User**

```
👥 Manajemen User
├── ➕ **CREATE** - Tambah user baru
├── 👁️ **READ** - Lihat data user
├── ✏️ **UPDATE** - Edit data user
├── 🗑️ **DELETE** - Hapus user
├️ 🎭 **UPDATE** - Update role & permission
└️ 🔄 **UPDATE** - Aktif/non-aktif user
```

## 🎯 **KESIMPULAN CRUD OPERATIONS**

### **MODUL DENGAN CRUD LENGKAP:**

✅ **Manajemen Pasien** - Full CRUD  
✅ **Modul Konsultasi** - Full CRUD  
✅ **Layanan & Harga** - Full CRUD  
✅ **Manajemen Pesanan** - Full CRUD  
✅ **Manajemen Pembayaran** - Full CRUD  
✅ **Manajemen Inventori** - Full CRUD (Phase 3)  
✅ **Pengaturan Sistem** - Full CRUD

### **MODUL READ-ONLY:**

📊 **Dashboard** - READ only  
📈 **Laporan & Analytics** - READ only  
📊 **Statistik Pasien** - READ only

### **POLA UMUM SETIAP MODUL:**

```
1. INDEX   -> READ (menampilkan list data)
2. CREATE  -> CREATE (form tambah data baru)
3. STORE   -> CREATE (proses simpan ke database)
4. SHOW    -> READ (menampilkan detail)
5. EDIT    -> READ (menampilkan form edit)
6. UPDATE  -> UPDATE (proses update ke database)
7. DESTROY -> DELETE (proses hapus data)
```

Jadi **YA**, hampir semua menu dan submenu memiliki operasi CRUD lengkap kecuali modul laporan dan dashboard yang bersifat read-only untuk analisis data.
