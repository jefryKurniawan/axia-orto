# Panduan Pengguna Sistem Axia Orto

**Sistem Informasi Manajemen Klinik Ortotik & Prostetik**

Dokumen ini ditujukan untuk **Owner** dan **Admin Internal** klinik Axia Orto agar dapat menggunakan seluruh fitur sistem dengan benar dan mandiri.

---

## Daftar Isi

1. [Masuk dan Keluar Sistem](#1-masuk-dan-keluar-sistem)
2. [Halaman Utama (Dashboard)](#2-halaman-utama-dashboard)
3. [Kelola Data Pasien](#3-kelola-data-pasien)
4. [Kelola Konsultasi](#4-kelola-konsultasi)
5. [Kelola Daftar Layanan](#5-kelola-daftar-layanan)
6. [Kelola Order Perawatan](#6-kelola-order-perawatan)
7. [Kelola Pembayaran](#7-kelola-pembayaran)
8. [Kelola Produksi](#8-kelola-produksi)
9. [Kelola Inventaris (Stok Barang)](#9-kelola-inventaris-stok-barang)
10. [Melihat dan Mengunduh Laporan](#10-melihat-dan-mengunduh-laporan)
11. [Catatan Perubahan Data (Audit Log)](#11-catatan-perubahan-data-audit-log)
12. [Membuat Akun Pengguna Baru](#12-membuat-akun-pengguna-baru)
13. [Hak Akses Tiap Peran](#13-hak-akses-tiap-peran)
14. [Tips dan Pemecahan Masalah](#14-tips-dan-pemecahan-masalah)

---

## 1. Masuk dan Keluar Sistem

### Cara Masuk

1. Buka browser internet (Google Chrome, Mozilla Firefox, atau Microsoft Edge).
2. Ketik alamat website klinik di kolom alamat browser.
3. Halaman depan klinik akan tampil. Klik tombol **Masuk** di pojok kanan atas.
4. Anda akan diarahkan ke halaman login.
5. Masukkan **alamat email** dan **kata sandi** yang sudah diberikan oleh admin.
6. Klik tombol **Masuk**.
7. Jika email dan kata sandi benar, Anda akan masuk ke halaman utama (Dashboard).

### Cara Keluar

1. Di pojok kiri bawah layar (bagian menu samping), Anda akan melihat nama Anda dan peran Anda (misal: Admin, Dokter, Staf Klinik).
2. Klik nama Anda tersebut, lalu pilih **Keluar**.
3. Anda akan kembali ke halaman depan klinik.

### Jika Lupa Kata Sandi

Hubungi admin sistem untuk mengatur ulang kata sandi Anda.

### Jika Salah Memasukkan Kata Sandi

Sistem akan menampilkan pesan bahwa email atau kata sandi salah. Periksa kembali penulisan email dan kata sandi Anda. Pastikan huruf besar dan kecil sudah benar.

---

## 2. Halaman Utama (Dashboard)

Setelah masuk, Anda akan langsung berada di halaman Dashboard. Halaman ini menampilkan ringkasan seluruh aktivitas klinik dalam satu layar.

### Apa Saja yang Tampil di Dashboard

**Kartu Ringkasan Angka (di bagian atas):**

| Kartu | Penjelasan |
|-------|-----------|
| Konsultasi Hari Ini | Berapa banyak konsultasi yang sudah dijadwalkan hari ini, dan berapa yang sudah selesai. |
| Total Pasien | Jumlah seluruh pasien yang tercatat di sistem. |
| Dokter Aktif | Jumlah dokter yang terdaftar di sistem. |
| Pasien Baru | Berapa pasien baru yang mendaftar bulan ini. |
| Stok Rendah | Jumlah barang di inventaris yang stoknya sudah di bawah batas minimum. Jika angkanya lebih dari 0, artinya ada barang yang perlu segera diisi ulang. |

**Tombol Cepat (di bawah kartu ringkasan):**

- **Tambah Pasien** — Langsung menuju form pendaftaran pasien baru.
- **Tambah Konsultasi** — Langsung menuju form membuat konsultasi baru.
- **Tambah Order** — Langsung menuju form membuat order baru.

**Status Konsultasi Hari Ini (bagian tengah):**

Menampilkan grafik batang yang menunjukkan jumlah konsultasi hari ini berdasarkan status:
- Dijadwalkan (sudah tercatat tapi belum dimulai)
- Berlangsung (sedang dalam proses)
- Selesai (sudah selesai)
- Dibatalkan

**Konsultasi Terbaru (bagian kanan):**

Menampilkan 5 konsultasi terakhir yang dicatat di sistem, lengkap dengan nama pasien, keluhan, dan statusnya.

**Grafik (bagian bawah):**

- **Pendapatan 30 Hari** — Grafik garis yang menunjukkan pemasukan klinik selama 30 hari terakhir.
- **Konsultasi 30 Hari** — Grafik batang yang menunjukkan jumlah konsultasi selama 30 hari terakhir.
- **Status Order** — Grafik lingkaran yang menunjukkan pembagian order berdasarkan status (Draft, Dikonfirmasi, Produksi, Siap, Dikirim, Dibatalkan).
- **Pipeline Produksi** — Grafik yang menunjukkan alur produksi yang sedang berjalan.

**Peringatan Stok Rendah (bagian bawah):**

Jika ada barang di inventaris yang stoknya di bawah batas minimum, akan muncul kartu peringatan berwarna kuning atau merah. Kartu merah artinya stok sudah sangat rendah (kurang dari 50% batas minimum). Klik **Lihat inventaris** untuk langsung menuju halaman inventaris.

---

## 3. Kelola Data Pasien

Menu **Pasien** digunakan untuk mendaftarkan pasien baru, mencari data pasien, melihat detail pasien, mengedit data, dan menghapus data.

### 3.1 Melihat Daftar Pasien

1. Klik menu **Pasien** di menu samping (sebelah kiri layar).
2. Daftar seluruh pasien akan tampil dalam bentuk tabel.
3. Setiap baris menampilkan: Nomor Rekam Medis, Nama Pasien, Jenis Kelamin, Jenis Asuransi, dan Nomor Telepon.
4. Gunakan tombol panah di bagian bawah tabel untuk berpindah halaman jika data pasien banyak.

### 3.2 Mencari Pasien

1. Di bagian atas daftar pasien, terdapat kolom pencarian bertuliskan **Cari nama, NIK, atau No. RM...**
2. Ketik nama pasien, nomor NIK, atau nomor rekam medis yang ingin dicari.
3. Hasil pencarian akan muncul secara otomatis.
4. Untuk menghapus kata pencarian, klik tombol silang (X) di ujung kolom pencarian.

### 3.3 Mendaftarkan Pasien Baru

1. Klik tombol **+ Tambah Pasien** di pojok kanan atas.
2. Isi formulir pendaftaran:

**Bagian Informasi Dasar:**
- **Nama Lengkap** (wajib diisi) — Nama lengkap pasien.
- **NIK** — Nomor Induk Kependudukan 16 digit. Tidak wajib, tetapi sangat dianjurkan diisi.
- **Tanggal Lahir** (wajib diisi) — Klik kolom ini lalu pilih tanggal lahir pasien dari kalender yang muncul.
- **Jenis Kelamin** (wajib diisi) — Pilih Laki-laki atau Perempuan dari daftar pilihan.

**Bagian Kontak dan Alamat:**
- **Telepon** — Nomor telepon pasien yang bisa dihubungi.
- **Asuransi** (wajib diisi) — Pilih jenis asuransi pasien: BPJS, Mandiri, atau Asuransi.
- **Alamat** — Alamat lengkap tempat tinggal pasien.
- **Kontak Darurat** — Nama dan nomor telepon keluarga atau kerabat yang bisa dihubungi jika terjadi keadaan darurat.
- **Golongan Darah** — Pilih golongan darah pasien (A, B, AB, O). Bisa dikosongkan jika tidak diketahui.

3. Setelah semua kolom wajib terisi, klik tombol **Tambah Pasien** di bagian bawah.
4. Jika berhasil, Anda akan kembali ke halaman daftar pasien dan melihat pemberitahuan hijau di pojok kanan atas yang bertuliskan **"Pasien baru berhasil ditambahkan."**
5. Jika ada kolom yang belum diisi dengan benar, kolom tersebut akan ditandai dengan tulisan merah di bawahnya. Perbaiki isian tersebut lalu klik tombol simpan lagi.

### 3.4 Melihat Detail Pasien

1. Di halaman daftar pasien, klik **ikon mata** di kolom paling kanan pada baris pasien yang ingin dilihat.
2. Halaman detail pasien akan tampil, menampilkan semua informasi: nomor rekam medis, NIK, tanggal lahir, jenis kelamin, telepon, alamat, kontak darurat, golongan darah, alergi, jenis asuransi, dan tanggal pendaftaran.

### 3.5 Mengedit Data Pasien

1. Buka detail pasien terlebih dahulu (lihat cara 3.4).
2. Klik tombol **Edit** di pojok kanan atas halaman detail.
3. Formulir edit akan tampil dengan data yang sudah terisi sebelumnya.
4. Ubah data yang perlu diperbaiki.
5. Klik tombol **Simpan Perubahan**.
6. Jika berhasil, Anda akan kembali ke halaman daftar pasien dengan pemberitahuan hijau.

### 3.6 Menghapus Pasien

1. Buka detail pasien terlebih dahulu.
2. Klik tombol **Hapus** (berwarna merah) di pojok kanan atas.
3. Akan muncul jendela konfirmasi: **"Yakin ingin menghapus pasien [nama pasien]?"**
4. Klik **Hapus** untuk menghapus, atau **Batal** untuk membatalkan.
5. Data pasien yang sudah dihapus tidak bisa dikembalikan. Pastikan Anda benar-benar yakin sebelum menghapus.

### 3.7 Mengimpor Banyak Data Pasien Sekaligus (Import CSV)

Jika Anda punya data pasien yang banyak dan sudah tercatat di file Excel atau spreadsheet, Anda bisa mengimpor semuanya sekaligus tanpa perlu mengetik satu per satu.

1. Klik tombol **Import CSV** di pojok kanan atas halaman daftar pasien.
2. Jika Anda belum punya file yang sesuai formatnya, klik tombol **Template** untuk mengunduh file contoh.
3. Buka file template tersebut di Microsoft Excel atau Google Sheets.
4. Isi data pasien sesuai kolom yang tersedia:
   - `name` — Nama lengkap pasien
   - `nik` — Nomor NIK (16 digit)
   - `medical_record_number` — Nomor rekam medis
   - `date_of_birth` — Tanggal lahir (format: YYYY-MM-DD, contoh: 1990-05-15)
   - `gender` — Jenis kelamin (L untuk Laki-laki, P untuk Perempuan)
   - `phone` — Nomor telepon
   - `address` — Alamat
   - `insurance_type` — Jenis asuransi (bpjs, mandiri, atau asuransi)
   - `blood_type` — Golongan darah (A, B, AB, atau O)
5. Simpan file sebagai format CSV (di Excel: File → Save As → pilih CSV).
6. Kembali ke halaman import di sistem, seret file CSV ke area titik-titik putus, atau klik area tersebut untuk memilih file dari komputer.
7. Klik tombol **Import**.
8. Tunggu hingga proses selesai. Sistem akan menampilkan hasilnya: berapa pasien yang berhasil diimpor dan berapa baris yang dilewati.

---

## 4. Kelola Konsultasi

Menu **Konsultasi** digunakan untuk mencatat setiap pertemuan antara pasien dan dokter, termasuk keluhan, diagnosis, dan rencana perawatan.

### 4.1 Melihat Daftar Konsultasi

1. Klik menu **Konsultasi** di menu samping.
2. Daftar seluruh konsultasi akan tampil.
3. Setiap baris menampilkan: Tanggal Konsultasi, Nama Pasien, Dokter, Keluhan, dan Status.
4. Anda bisa mencari berdasarkan nama pasien atau keluhan menggunakan kolom pencarian di bagian atas.
5. Anda bisa memfilter berdasarkan status menggunakan dropdown filter di bagian atas.

### 4.2 Membuat Konsultasi Baru

1. Klik tombol **+ Tambah Konsultasi** di pojok kanan atas.
2. Isi formulir:

**Bagian Informasi Konsultasi:**
- **Pasien** (wajib diisi) — Ketik nama pasien di kolom pencarian, lalu pilih pasien dari daftar yang muncul. Pastikan pasien sudah terdaftar di sistem terlebih dahulu.
- **Dokter** (wajib diisi) — Pilih dokter yang akan menangani konsultasi dari daftar pilihan.
- **Tanggal Konsultasi** (wajib diisi) — Pilih tanggal konsultasi dilakukan.
- **Status** (wajib diisi) — Pilih status konsultasi. Untuk konsultasi baru yang belum dimulai, pilih **Dijadwalkan**.

**Bagian Detail Medis:**
- **Keluhan Pasien** (wajib diisi) — Tuliskan keluhan yang disampaikan oleh pasien.
- **Diagnosis** — Tuliskan hasil diagnosis dokter setelah pemeriksaan.
- **Rencana Perawatan** — Tuliskan rencana tindakan yang akan dilakukan terhadap pasien.

**Bagian Jadwal dan Catatan:**
- **Tanggal Kontrol Ulang** — Jika pasien perlu kembali untuk pemeriksaan ulang, pilih tanggalnya di sini.
- **Catatan Tambahan** — Tuliskan catatan lain yang perlu disimpan.

3. Klik tombol **Tambah Konsultasi** di bagian bawah.
4. Jika berhasil, Anda akan kembali ke halaman daftar konsultasi.

### 4.3 Melihat Detail Konsultasi

1. Klik baris konsultasi yang ingin dilihat di halaman daftar konsultasi.
2. Halaman detail akan tampil menampilkan seluruh informasi: tanggal, nama pasien, nomor rekam medis, dokter, keluhan, diagnosis, rencana perawatan, catatan, tanggal kontrol ulang, dan status.

### 4.4 Mengedit Konsultasi

1. Buka detail konsultasi terlebih dahulu.
2. Klik tombol **Edit** di pojok kanan atas.
3. Ubah data yang diperlukan. Misalnya, ubah status dari **Dijadwalkan** menjadi **Selesai** setelah konsultasi selesai, atau isi diagnosis dan rencana perawatan yang sebelumnya kosong.
4. Klik tombol **Simpan Perubahan**.

### 4.5 Menghapus Konsultasi

1. Buka detail konsultasi.
2. Klik tombol **Hapus**.
3. Konfirmasi penghapusan pada jendela yang muncul.

### 4.6 Alur Kerja Konsultasi yang Umum

Berikut alur yang biasa dilakukan di klinik:

1. Pasien datang ke klinik → staf membuat konsultasi baru dengan status **Dijadwalkan**.
2. Saat pasien bertemu dokter → ubah status menjadi **Berlangsung**.
3. Dokter memeriksa pasien, lalu mengisi kolom keluhan, diagnosis, dan rencana perawatan.
4. Setelah selesai → ubah status menjadi **Selesai**.
5. Jika ada jadwal kontrol ulang → tentukan tanggal kontrol ulang.

---

## 5. Kelola Daftar Layanan

Menu **Layanan** digunakan untuk mengatur daftar layanan yang tersedia di klinik beserta harganya. Contoh layanan: Konsultasi Ortotik, Pembuatan KAFO, Terapi Fisik, dan lain-lain.

### 5.1 Melihat Daftar Layanan

1. Klik menu **Layanan** di menu samping.
2. Daftar seluruh layanan akan tampil.
3. Setiap baris menampilkan: Kode Layanan, Nama, Tipe, Harga, Durasi (hari), dan Status (Aktif/Nonaktif).

### 5.2 Menambahkan Layanan Baru

1. Klik tombol **+ Tambah Layanan** di pojok kanan atas.
2. Isi formulir:
   - **Nama Layanan** (wajib diisi) — Contoh: "Konsultasi Ortotik", "Pembuatan KAFO".
   - **Tipe Layanan** (wajib diisi) — Pilih jenis layanan: Konsultasi, Ortosis, Protesis, Terapi, atau Alat.
   - **Harga (Rp)** (wajib diisi) — Masukkan harga layanan dalam Rupiah. Cukup tulis angka saja tanpa titik atau tanda Rupiah, contoh: 150000.
   - **Durasi (hari)** — Perkiraan waktu pengerjaan layanan dalam satuan hari. Tidak wajib.
   - **Layanan aktif** — Centang jika layanan ini masih ditawarkan. Hapus centang jika layanan sudah tidak aktif.
   - **Deskripsi** — Penjelasan singkat tentang layanan. Tidak wajib.
3. Klik tombol **Tambah Layanan**.

### 5.3 Mengedit Layanan

1. Di halaman daftar layanan, klik **ikon pensil** pada baris layanan yang ingin diubah.
2. Ubah data yang diperlukan.
3. Klik **Simpan Perubahan**.

### 5.4 Menghapus Layanan

1. Klik **ikon tong sampah** pada baris layanan yang ingin dihapus.
2. Konfirmasi penghapusan pada jendela yang muncul.

---

## 6. Kelola Order Perawatan

Menu **Order** digunakan untuk mencatat pesanan perawatan atau pembuatan alat untuk pasien. Order biasanya dibuat setelah konsultasi selesai dan dokter sudah menentukan rencana perawatan.

### 6.1 Melihat Daftar Order

1. Klik menu **Order** di menu samping.
2. Daftar seluruh order akan tampil.
3. Setiap baris menampilkan: Nomor Order, Nama Pasien, Tanggal Order, Total Harga, dan Status.
4. Anda bisa mencari berdasarkan nomor order atau nama pasien.
5. Anda bisa memfilter berdasarkan status order.

### 6.2 Membuat Order Baru

1. Klik tombol **+ Tambah Order** di pojok kanan atas.
2. Isi formulir:

**Bagian Informasi Order:**
- **Pasien** (wajib diisi) — Pilih pasien dari daftar dropdown.
- **Tanggal Order** (wajib diisi) — Sudah otomatis terisi dengan tanggal hari ini. Bisa diubah jika diperlukan.
- **Tanggal Pengiriman** — Perkiraan tanggal alat atau perawatan selesai dan bisa diserahkan ke pasien.
- **Catatan** — Catatan tambahan tentang order ini.

**Bagian Layanan:**
- **Layanan** (wajib diisi) — Pilih layanan yang dipesan dari daftar dropdown. Nama layanan dan harganya akan tampil.
- **Jumlah** — Berapa banyak layanan yang dipesan. Nilai default adalah 1.
- Jika pasien memesan lebih dari satu layanan, klik tombol **+ Tambah** untuk menambah baris layanan baru.
- Klik **ikon tong sampah** untuk menghapus baris layanan yang tidak diperlukan.

3. Klik tombol **Buat Order**.

### 6.3 Melihat Detail Order

1. Klik **ikon mata** pada baris order yang ingin dilihat.
2. Halaman detail akan tampil menampilkan: nomor order, nama pasien, tanggal order, tanggal pengiriman, status, daftar layanan yang dipesan beserta harga, total pembayaran, dan catatan.

### 6.4 Mengubah Status Order

Di halaman detail order, terdapat tombol-tombol untuk memajukan status order secara berurutan:

- **Konfirmasi** — Mengubah status dari Draft menjadi Dikonfirmasi.
- **Mulai Produksi** — Mengubah status dari Dikonfirmasi menjadi Produksi.
- **Tandai Siap** — Mengubah status dari Produksi menjadi Siap.
- **Kirim** — Mengubah status dari Siap menjadi Dikirim.

### 6.5 Mengedit Order

1. Buka detail order, lalu klik tombol **Edit**.
2. Ubah data yang diperlukan.
3. Klik **Simpan Perubahan**.

### 6.6 Menghapus Order

1. Di halaman daftar order, klik **ikon tong sampah** pada baris order yang ingin dihapus.
2. Konfirmasi penghapusan.
3. Order hanya bisa dihapus jika statusnya masih **Draft**.

### 6.7 Alur Kerja Order yang Umum

1. Dokter selesai konsultasi dan menentukan rencana perawatan.
2. Staf membuat order berdasarkan rencana perawatan dari dokter. Status order: **Draft**.
3. Admin atau staf senior mengonfirmasi order. Status berubah: **Dikonfirmasi**.
4. Teknisi mulai mengerjakan alat. Status berubah: **Produksi**.
5. Alat selesai dibuat. Status berubah: **Siap**.
6. Pasien mengambil alat. Status berubah: **Dikirim**.

---

## 7. Kelola Pembayaran

Menu **Pembayaran** digunakan untuk mencatat setiap pembayaran yang dilakukan oleh pasien untuk order yang sudah dibuat.

### 7.1 Melihat Daftar Pembayaran

1. Klik menu **Pembayaran** di menu samping.
2. Daftar seluruh pembayaran akan tampil.
3. Setiap baris menampilkan: Nomor Pembayaran, Nomor Order, Nama Pasien, Jumlah Bayar, Metode Pembayaran, dan Status.
4. Anda bisa mencari berdasarkan nomor pembayaran atau nomor order.
5. Anda bisa memfilter berdasarkan status pembayaran.

### 7.2 Mencatat Pembayaran Baru

1. Klik tombol **+ Tambah Pembayaran** di pojok kanan atas.
2. Isi formulir:
   - **Order** (wajib diisi) — Pilih order yang akan dibayar dari daftar dropdown. Daftar menampilkan nomor order dan nama pasien.
   - **Tanggal Pembayaran** (wajib diisi) — Sudah otomatis terisi dengan tanggal hari ini.
   - **Metode Pembayaran** (wajib diisi) — Pilih cara bayar: Tunai, Transfer, Kartu Debit, atau Kartu Kredit.
   - **Jumlah (Rp)** (wajib diisi) — Nominal pembayaran dalam Rupiah. Tulis angka saja tanpa titik atau tanda Rupiah.
   - **Catatan** — Catatan tambahan tentang pembayaran. Tidak wajib.
3. Klik tombol **Simpan**.

### 7.3 Mengedit Pembayaran

1. Di halaman daftar pembayaran, klik baris pembayaran yang ingin diubah.
2. Ubah data yang diperlukan.
3. Klik **Simpan Perubahan**.

### 7.4 Menghapus Pembayaran

1. Klik **ikon tong sampah** pada baris pembayaran yang ingin dihapus.
2. Konfirmasi penghapusan.
3. Pembayaran hanya bisa dihapus jika statusnya masih **Pending**.

---

## 8. Kelola Produksi

Menu **Produksi** digunakan untuk melacak setiap tahapan pembuatan alat ortotik atau prostetik. Setiap order bisa memiliki beberapa tahapan produksi yang dikerjakan oleh teknisi.

### 8.1 Melihat Daftar Tracking Produksi

1. Klik menu **Produksi** di menu samping.
2. Daftar seluruh catatan produksi akan tampil.
3. Setiap baris menampilkan: Order Terkait, Nama Pasien, Langkah Produksi, Teknisi, dan Status.
4. Anda bisa mencari berdasarkan nomor order.
5. Anda bisa memfilter berdasarkan status produksi.

### 8.2 Menambahkan Catatan Produksi Baru

1. Klik tombol **+ Tambah Tracking** di pojok kanan atas.
2. Isi formulir:
   - **Order** (wajib diisi) — Pilih order yang sedang diproduksi dari daftar dropdown.
   - **Langkah Produksi** (wajib diisi) — Tuliskan nama tahapan produksi. Contoh: "Pengukuran", "Cetak 3D", "Assembly", "Finishing", "Quality Check".
   - **Teknisi** (wajib diisi) — Pilih teknisi yang mengerjakan tahapan ini dari daftar dropdown.
   - **Catatan** — Catatan tentang tahapan produksi ini. Tidak wajib.
3. Klik tombol **Tambah Tracking**.

### 8.3 Melihat Detail dan Mengubah Status Produksi

1. Klik **ikon mata** pada baris produksi yang ingin dilihat.
2. Detail akan tampil menampilkan: order terkait, langkah produksi, teknisi, catatan, dan status.
3. Untuk mengubah status:
   - Klik **Mulai** untuk mengubah status dari Pending menjadi Sedang Dikerjakan.
   - Klik **Selesai** untuk mengubah status dari Sedang Dikerjakan menjadi Selesai.

### 8.4 Mengedit Catatan Produksi

1. Buka detail produksi, lalu klik tombol **Edit**.
2. Ubah data yang diperlukan.
3. Klik **Simpan Perubahan**.
4. Catatan produksi yang sudah selesai tidak bisa diedit lagi.

### 8.5 Menghapus Catatan Produksi

1. Di halaman daftar produksi, klik **ikon tong sampah** pada baris yang ingin dihapus.
2. Konfirmasi penghapusan.

### 8.6 Alur Kerja Produksi yang Umum

1. Order sudah dikonfirmasi oleh admin.
2. Admin atau teknisi membuat catatan produksi pertama, misalnya "Pengukuran" dengan teknisi yang ditugaskan.
3. Teknisi mulai mengerjakan tahap tersebut → status diubah menjadi **Sedang Dikerjakan**.
4. Setelah tahap selesai → status diubah menjadi **Selesai**.
5. Buat catatan produksi baru untuk tahap berikutnya, misalnya "Cetak 3D".
6. Ulangi proses hingga semua tahapan selesai.

---

## 9. Kelola Inventaris (Stok Barang)

Menu **Inventaris** digunakan untuk mengelola stok bahan dan material yang digunakan di klinik, seperti plastazote, kain orthowrap, sekrup, dan lain-lain.

### 9.1 Melihat Daftar Inventaris

1. Klik menu **Inventaris** di menu samping.
2. Di bagian atas, terdapat kartu ringkasan:
   - **Total Item** — Jumlah seluruh item di inventaris.
   - **Aktif** — Jumlah item yang masih aktif.
   - **Stok Rendah** — Jumlah item yang stoknya di bawah batas minimum.
   - **Nilai Inventory** — Total nilai seluruh inventaris dalam Rupiah.
3. Daftar item akan tampil di bawahnya.
4. Setiap baris menampilkan: Kode, Nama, Kategori, Stok, Satuan, Harga, dan Status.
5. Item yang stoknya rendah akan ditandai dengan garis merah di sebelah kiri baris dan badge **Stok Rendah**.
6. Anda bisa mencari berdasarkan kode atau nama item.
7. Anda bisa memfilter berdasarkan kategori: Bahan Baku, Komponen, atau Alat Jadi.

### 9.2 Menambahkan Item Inventaris Baru

1. Klik tombol **+ Tambah Item** di pojok kanan atas.
2. Isi formulir:

**Bagian Informasi Dasar:**
- **Kode** (wajib diisi) — Kode unik untuk item. Contoh: "INV-001", "PLT-A5".
- **Nama** (wajib diisi) — Nama item. Contoh: "Plastazote 5mm", "Kain Orthowrap".
- **Deskripsi** — Penjelasan tentang item. Tidak wajib.

**Bagian Kategori dan Stok:**
- **Kategori** (wajib diisi) — Pilih: Bahan Baku, Komponen, atau Alat Jadi.
- **Satuan** (wajib diisi) — Satuan pengukuran. Contoh: "pcs", "kg", "meter", "lembar".
- **Stok Awal** (wajib diisi) — Jumlah stok saat pertama kali dicatat.
- **Batas Minimum** (wajib diisi) — Jumlah stok minimum. Jika stok di bawah angka ini, sistem akan menampilkan peringatan.
- **Harga (Rp)** (wajib diisi) — Harga per satuan.
- **Item Aktif** — Centang jika item masih digunakan. Hapus centang jika sudah tidak dipakai.

3. Klik tombol **Tambah Item**.

### 9.3 Melihat Detail Item dan Riwayat Stok

1. Klik **ikon mata** pada baris item yang ingin dilihat.
2. Halaman detail akan tampil menampilkan:
   - Informasi item: kode, nama, kategori, satuan, stok saat ini, batas minimum, harga, dan deskripsi.
   - **Peringatan stok rendah** (jika stok di bawah batas minimum) — berupa kotak berwarna oranye.
   - **Panel Penyesuaian Stok** — untuk menambah atau mengurangi stok.
   - **Riwayat Transaksi** — daftar seluruh perubahan stok yang pernah terjadi, lengkap dengan tanggal, tipe, jumlah, catatan, dan nama pengguna.

### 9.4 Menyesuaikan Stok (Menambah atau Mengurangi)

1. Buka detail item inventaris.
2. Di bagian **Penyesuaian Stok**, pilih tipe:
   - **Stok Masuk** — Untuk mencatat penerimaan barang baru.
   - **Stok Keluar** — Untuk mencatat penggunaan barang.
   - **Adjustment** — Untuk mengoreksi stok (misalnya setelah stok opname).
3. Masukkan **Jumlah**.
4. Tuliskan **Catatan** (opsional) — misalnya "Terima dari supplier ABC" atau "Digunakan untuk order #ORD-001".
5. Klik tombol untuk menyimpan.
6. Stok akan otomatis bertambah atau berkurang sesuai tipe yang dipilih.

### 9.5 Mengedit Item Inventaris

1. Buka detail item, lalu klik tombol **Edit**.
2. Ubah data yang diperlukan.
3. Klik **Simpan Perubahan**.

### 9.6 Menghapus Item Inventaris

1. Di halaman daftar inventaris, klik **ikon tong sampah** pada baris item yang ingin dihapus.
2. Konfirmasi penghapusan.

---

## 10. Melihat dan Mengunduh Laporan

Menu **Laporan** digunakan untuk membuat dan mengunduh laporan data klinik dalam format file CSV. File CSV bisa dibuka di Microsoft Excel atau Google Sheets.

### 10.1 Mengatur Rentang Tanggal

Sebelum membuat laporan, tentukan rentang tanggal yang ingin dilaporkan:

1. Di bagian **Dari**, pilih tanggal awal.
2. Di bagian **Sampai**, pilih tanggal akhir.
3. Secara default, sistem sudah mengatur rentang 30 hari terakhir.

### 10.2 Jenis Laporan yang Tersedia

| Jenis Laporan | Isi Laporan |
|---------------|-------------|
| **Pendapatan** | Ringkasan pendapatan harian berdasarkan metode pembayaran (tunai, transfer, kartu debit, kartu kredit). |
| **Pasien & Konsultasi** | Data pasien baru dan riwayat konsultasi dalam rentang tanggal yang dipilih. |
| **Order & Produksi** | Status order perawatan dan catatan tracking produksi. |
| **Pembayaran** | Daftar transaksi pembayaran masuk dan yang masih tertunggak. |

### 10.3 Membuat dan Mengunduh Laporan

1. Pilih rentang tanggal yang diinginkan.
2. Klik tombol **Export CSV** pada jenis laporan yang ingin diunduh.
3. Sistem akan memproses laporan di belakang layar. Tunggu beberapa saat.
4. Laporan yang sedang diproses akan muncul di bagian **Riwayat Export** di bawah dengan status **Menunggu** atau **Diproses**.
5. Setelah selesai, status akan berubah menjadi **Selesai** dan tombol **Download** akan muncul.
6. Klik tombol **Download** untuk mengunduh file CSV.
7. File CSV akan tersimpan di komputer Anda. Buka file tersebut dengan Excel atau Google Sheets.

### 10.4 Melihat Riwayat Export

Di bagian **Riwayat Export**, Anda bisa melihat semua laporan yang pernah dibuat:

- **Tipe** — Jenis laporan (Pendapatan, Pasien & Konsultasi, Order & Produksi, Pembayaran).
- **Tanggal** — Waktu laporan dibuat.
- **Status** — Status proses: Menunggu, Diproses, Selesai, atau Gagal.
- **Aksi** — Tombol Download (hanya muncul jika status sudah Selesai).

Jika status **Gagal**, coba buat laporan ulang dengan rentang tanggal yang lebih pendek.

---

## 11. Catatan Perubahan Data (Audit Log)

Menu **Audit Log** hanya bisa diakses oleh pengguna dengan peran **Admin**. Menu ini mencatat setiap perubahan data yang dilakukan oleh semua pengguna di sistem, sehingga owner bisa memantau siapa yang mengubah data apa dan kapan.

### 11.1 Melihat Catatan Perubahan

1. Klik menu **Audit Log** di menu samping.
2. Daftar catatan perubahan akan tampil.
3. Setiap baris menampilkan: Waktu, Nama Pengguna, Data yang Diubah, Jenis Perubahan, dan Alamat IP.

### 11.2 Memfilter Catatan

Anda bisa memfilter catatan untuk mempermudah pencarian:

- **Jenis Data** — Pilih dari dropdown: Pasien, Konsultasi, Order, Pembayaran, atau Inventaris. Pilih "Semua Model" untuk melihat semua.
- **Tanggal** — Tentukan rentang tanggal dengan kolom **Dari** dan **Sampai**.

### 11.3 Melihat Detail Perubahan

1. Klik baris catatan yang ingin dilihat detailnya.
2. Detail perubahan akan tampil di bawah baris tersebut:
   - **Dibuat** — Menampilkan semua data yang dicatat pertama kali (ditulis dengan warna hijau).
   - **Diubah** — Menampilkan data lama (ditulis dengan warna merah dan dicoret) dan data baru (ditulis dengan warna hijau).
   - **Dihapus** — Menampilkan data yang dihapus (ditulis dengan warna merah dan dicoret).
3. Klik lagi baris yang sama untuk menutup detail.

### 11.4 Jenis Perubahan yang Dicatat

| Jenis Perubahan | Keterangan |
|----------------|-----------|
| Dibuat | Data baru ditambahkan (misal: pasien baru didaftarkan). |
| Diubah | Data yang sudah ada diperbarui (misal: alamat pasien diubah). |
| Dihapus | Data dihapus dari sistem (misal: konsultasi dihapus). |

---

## 12. Membuat Akun Pengguna Baru

Hanya pengguna dengan peran **Admin** yang bisa membuat akun pengguna baru.

### Cara Membuat Akun Baru

1. Klik menu **Register** di menu samping (jika tersedia), atau akses langsung halaman pendaftaran.
2. Isi formulir:
   - **Nama** (wajib diisi) — Nama lengkap pengguna.
   - **Email** (wajib diisi) — Alamat email yang akan digunakan untuk masuk ke sistem.
   - **Kata Sandi** (wajib diisi) — Kata sandi untuk masuk. Minimal 8 karakter.
   - **Peran** (wajib diisi) — Pilih peran pengguna: Dokter, Staf Klinik, atau Teknisi.
   - **Spesialisasi** — Untuk dokter, tuliskan bidang spesialisasi. Tidak wajib.
   - **Telepon** — Nomor telepon pengguna. Tidak wajib.
3. Klik tombol untuk mendaftarkan akun.
4. Berikan email dan kata sandi kepada pengguna yang bersangkutan.

### Peran yang Tersedia

- **Dokter** — Untuk dokter yang menangani konsultasi pasien.
- **Staf Klinik** — Untuk staf administrasi yang mengelola data pasien, order, dan pembayaran.
- **Teknisi** — Untuk teknisi yang mengerjakan produksi alat.

---

## 13. Hak Akses Tiap Peran

Setiap peran memiliki hak akses yang berbeda-beda. Berikut rincian lengkapnya:

### 13.1 Admin

Bisa mengakses **semua menu** dan melakukan **semua aksi** (tambah, lihat, edit, hapus) di semua modul. Hanya admin yang bisa:
- Melihat Audit Log.
- Membuat akun pengguna baru.
- Mengelola data layanan.

### 13.2 Dokter

| Menu | Bisa Tambah | Bisa Lihat | Bisa Edit | Bisa Hapus |
|------|:-----------:|:----------:|:---------:|:----------:|
| Pasien | - | Ya | - | - |
| Konsultasi | Ya | Ya | Ya | Ya |
| Layanan | - | Ya | - | - |
| Order | Ya | Ya | - | - |
| Pembayaran | - | - | - | - |
| Produksi | - | Ya | - | - |
| Inventaris | - | Ya | - | - |
| Laporan | - | Ya | - | - |

### 13.3 Staf Klinik

| Menu | Bisa Tambah | Bisa Lihat | Bisa Edit | Bisa Hapus |
|------|:-----------:|:----------:|:---------:|:----------:|
| Pasien | Ya | Ya | Ya | Ya |
| Konsultasi | - | Ya | - | - |
| Layanan | - | Ya | - | - |
| Order | Ya | Ya | Ya | - |
| Pembayaran | Ya | Ya | Ya | Ya |
| Produksi | - | Ya | - | - |
| Inventaris | Ya | Ya | - | - |
| Laporan | - | Ya | - | - |

### 13.4 Teknisi

| Menu | Bisa Tambah | Bisa Lihat | Bisa Edit | Bisa Hapus |
|------|:-----------:|:----------:|:---------:|:----------:|
| Order | - | Ya | - | - |
| Produksi | - | Ya | Ya | - |
| Inventaris | - | Ya | - | - |

Teknisi **tidak bisa** mengakses menu Pasien, Konsultasi, Layanan, Pembayaran, Laporan, dan Audit Log.

---

## 14. Tips dan Pemecahan Masalah

### Halaman Tidak Muncul atau Tampil Kosong

1. Periksa koneksi internet Anda.
2. Tekan tombol **F5** di keyboard untuk memuat ulang halaman.
3. Jika masih kosong, bersihkan cache browser:
   - **Chrome:** Klik titik tiga di pojok kanan atas → Hapus data penjelajahan → centang "Gambar dan file yang di-cache" → klik Hapus data.
   - **Firefox:** Klik garis tiga di pojok kanan atas → Pengaturan → Privasi & Keamanan → Cookies dan Data Situs → Hapus Data.

### Tidak Bisa Masuk ke Sistem

1. Pastikan email dan kata sandi yang dimasukkan benar.
2. Perhatikan huruf besar dan kecil pada kata sandi.
3. Pastikan tombol Caps Lock di keyboard tidak menyala.
4. Hubungi admin jika masih tidak bisa masuk.

### Data yang Baru Diinput Tidak Muncul

1. Coba muat ulang halaman dengan menekan **F5**.
2. Periksa apakah Anda memiliki hak akses untuk melihat data tersebut.

### Pemberitahuan Merah Muncul Saat Menyimpan Data

1. Baca pesan kesalahan yang tampil. Biasanya ada kolom yang belum diisi atau isian tidak sesuai format.
2. Periksa kolom yang ditandai dengan tulisan merah.
3. Perbaiki isian sesuai pesan kesalahan, lalu coba simpan lagi.

### Laporan Tidak Bisa Diunduh

1. Pastikan laporan sudah selesai diproses (status **Selesai** di Riwayat Export).
2. Jika status **Gagal**, coba buat laporan ulang dengan rentang tanggal yang lebih pendek.
3. Jika masih gagal, hubungi teknisi sistem.

### Tampilan Berantakan di HP

1. Coba muat ulang halaman.
2. Pastikan browser sudah diperbarui ke versi terbaru.
3. Gunakan browser Google Chrome atau Mozilla Firefox untuk hasil terbaik.

### Cara Beralih ke Tampilan Gelap (Dark Mode)

1. Klik ikon matahari atau bulan di pojok kanan atas layar.
2. Tampilan akan berubah menjadi gelap (latar belakang gelap, tulisan terang).
3. Klik lagi untuk kembali ke tampilan terang.
4. Pilihan Anda akan tersimpan secara otomatis.

### Cara Membuka dan Menutup Menu Samping

- **Di layar besar (laptop/komputer):** Klik ikon garis tiga (hamburger) di pojok kiri atas untuk mengecilkan atau memperbesar menu samping.
- **Di layar kecil (HP):** Klik ikon garis tiga untuk membuka menu samping sebagai panel melayang. Klik di luar panel atau klik ikon silang untuk menutupnya.

### Tabel Terpotong di Layar Kecil

Di layar HP, tabel akan otomatis berubah menjadi kartu-kartu yang lebih mudah dibaca. Jika tampilan terpotong, coba putar HP ke posisi mendatar (landscape).

---

## Keterangan Tambahan

### Tentang Notifikasi

Sistem akan menampilkan notifikasi (pemberitahuan kecil di pojok kanan atas) setiap kali Anda berhasil menyimpan, mengedit, atau menghapus data. Notifikasi hijau artinya berhasil, notifikasi merah artinya gagal.

### Tentang Pencarian

Semua halaman yang memiliki daftar data (Pasien, Konsultasi, Order, Pembayaran, Produksi, Inventaris) dilengkapi dengan kolom pencarian. Ketik kata kunci lalu sistem akan otomatis menampilkan hasil yang sesuai.

### Tentang Paginasi (Halaman)

Jika data yang tampil terlalu banyak, data akan dibagi menjadi beberapa halaman. Gunakan tombol angka di bagian bawah tabel untuk berpindah halaman. Informasi "Menampilkan X-Y dari Z data" akan tampil untuk menunjukkan posisi Anda.

### Tentang Mode Responsif

Sistem ini bisa digunakan di komputer, laptop, tablet, dan HP. Tampilan akan menyesuaikan secara otomatis dengan ukuran layar.

---

*Panduan ini berlaku untuk Axia Orto Clinic ERP versi 3.0.0. Jika ada pertanyaan atau menemui masalah yang tidak tercantum di panduan ini, silakan hubungi admin sistem atau teknisi yang bertanggung jawab.*
