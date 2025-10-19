Tentu, berikut adalah versi ringkas dari **README** yang hanya memuat bagian **"Tentang Axia Clinic ERP"** sesuai permintaan Anda, dengan format Markdown yang rapi:

# 🌟 Axia Clinic ERP - Sistem Manajemen Klinik Ortotik Prostetik

\<p align="center"\>\<img src="[https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg](https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg)" width="300" alt="Laravel Logo"\>\</p\>

\<p align="center"\>
\<a href="[https://github.com/laravel/framework/actions](https://github.com/laravel/framework/actions)"\>\<img src="[https://github.com/laravel/framework/workflows/tests/badge.svg](https://github.com/laravel/framework/workflows/tests/badge.svg)" alt="Build Status"\>\</a\>
\<a href="[https://packagist.org/packages/laravel/framework](https://packagist.org/packages/laravel/framework)"\>\<img src="[https://img.shields.io/packagist/dt/laravel/framework](https://img.shields.io/packagist/dt/laravel/framework)" alt="Total Downloads"\>\</a\>
\<a href="[https://packagist.org/packages/laravel/framework](https://packagist.org/packages/laravel/framework)"\>\<img src="[https://img.shields.io/packagist/v/laravel/framework](https://img.shields.io/packagist/v/laravel/framework)" alt="Latest Stable Version"\>\</a\>
\<a href="[https://packagist.org/packages/laravel/framework](https://packagist.org/packages/laravel/framework)"\>\<img src="[https://img.shields.io/packagist/l/laravel/framework](https://img.shields.io/packagist/l/laravel/framework)" alt="License"\>\</a\>
\</p\>

## 💡 Tentang Axia Clinic ERP

**Axia Clinic ERP** adalah sistem manajemen berbasis **Laravel 10** yang dirancang khusus untuk Klinik **Ortotik dan Prostetik**. Sistem ini mengintegrasikan manajemen pasien, penjadwalan dan rekam medis konsultasi, serta manajemen layanan spesifik ortotik/prostetik.

Fokus utama pengembangan adalah **kinerja tinggi** melalui implementasi **caching tingkat lanjut** menggunakan **Redis** untuk _session_, _cache_, dan _queue_, serta optimasi _caching_ pada lapisan **Eloquent Model** dan **API Response**.

Fitur utama yang diimplementasikan mencakup:

-   **Manajemen Pengguna** dengan peran spesifik: **Admin**, **Dokter**, dan **Staf Klinik**.
-   **Manajemen Pasien** yang komprehensif, mencakup data demografi, asuransi, dan riwayat medis penting (misalnya, alergi).
-   **Manajemen Konsultasi** terstruktur dengan status (terjadwal, berlangsung, selesai, dibatalkan).
-   **Manajemen Layanan** (Konsultasi, Ortotis, Prostesis, Terapi, Alat) dengan harga dan durasi.
-   **Optimasi Caching** menggunakan _tagging_ dan _custom TTL_ untuk menjaga kecepatan akses data.
