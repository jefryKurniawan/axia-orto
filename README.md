# 🌟 Axia Clinic ERP - Sistem Manajemen Klinik Ortotik Prostetik

## 💡 Tentang Axia Clinic ERP

**Axia Clinic ERP** adalah sistem manajemen berbasis **Laravel 10** yang dirancang khusus untuk Klinik **Ortotik dan Prostetik**. Sistem ini mengintegrasikan manajemen pasien, penjadwalan dan rekam medis konsultasi, serta manajemen layanan spesifik ortotik/prostetik.

Fokus utama pengembangan adalah **kinerja tinggi** melalui implementasi **caching tingkat lanjut** menggunakan **Redis** untuk _session_, _cache_, dan _queue_, serta optimasi _caching_ pada lapisan **Eloquent Model** dan **API Response**.

Fitur utama yang diimplementasikan mencakup:

-   **Manajemen Pengguna** dengan peran spesifik: **Admin**, **Dokter**, dan **Staf Klinik**.
-   **Manajemen Pasien** yang komprehensif, mencakup data demografi, asuransi, dan riwayat medis penting (misalnya, alergi).
-   **Manajemen Konsultasi** terstruktur dengan status (terjadwal, berlangsung, selesai, dibatalkan).
-   **Manajemen Layanan** (Konsultasi, Ortotis, Prostesis, Terapi, Alat) dengan harga dan durasi.
-   **Optimasi Caching** menggunakan _tagging_ dan _custom TTL_ untuk menjaga kecepatan akses data.
