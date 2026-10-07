# Sistem Pendukung Keputusan (SPK) Kelayakan Calon Nasabah KPR Metode SMART

Aplikasi Web **Sistem Pendukung Keputusan (SPK) Kelayakan Calon Nasabah Kredit Pemilikan Rumah (KPR)** dibangun menggunakan **Laravel 12 / PHP 8.2+**, **Metode SMART (Simple Multi Attribute Rating Technique)**, **Bootstrap 5**, **Chart.js**, **DataTables**, **SweetAlert2**, dan **DomPDF Engine**.

---

## ⚡ Fitur Utama System

1. **Mesin SMART Engine Transparan**:
   - Bobot Awal Kriteria ($W_j$)
   - Normalisasi Bobot ($w_j = W_j / \sum W_k$)
   - Matriks Nilai Utility ($u_j$) berdasarkan pencocokan Sub-Kriteria $[0 - 100]$
   - Perkalian Terbobot ($S(A_i) = \sum w_j \cdot u_j$)
   - Ranking Otomatis Pengajuan KPR
   - Keputusan Otomatis (DITERIMA / TIDAK DITERIMA) berdasarkan threshold (Default 80.00)
   - Reason Generator Otomatis (Analisis kekuatan & risiko finansial nasabah)

2. **Hak Akses Multi-Role (RBAC)**:
   - **Administrator**: Full access, Data Nasabah, Master Kriteria & Bobot, Sub Kriteria Utility, Mesin SMART Engine, User Management, System Threshold Settings.
   - **Manager KPR**: Executive Dashboard, Tinjau Detail Perhitungan SMART, Memberikan Persetujuan (Approve/Reject) + Catatan Manager, Cetak PDF Report.
   - **Calon Nasabah**: Registrasi, Lengkapi Biodata & Finansial, Form Pengajuan KPR + Upload Dokumen Persyaratan, Tracking Timeline Progres, Unduh PDF Decision.

3. **Dokumen & PDF Generator Resmi**:
   - Laporan PDF Keputusan KPR ber-QR Code Verifikasi Keaslian & Tanda Tangan Digital Manager.

---

## 🔑 Akun Demo Default

Database telah di-seed dengan akun demo berikut:

| Role Access | Email Login | Password | Akses Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@citra.com` | `password` | Dashboard Admin, Master Data, SMART Engine, Settings |
| **Marketing** | `marketing@citra.com` | `password` | Pengelolaan pengajuan dan data calon debitur |
| **Pimpinan** | `pimpinan@citra.com` | `password` | Dashboard pimpinan dan pemantauan hasil |
| **Debitur Demo** | Dipilih acak dari 34 akun demo di database | `password123` | Dashboard Nasabah, Form KPR & Upload Dokumen |

Halaman login menyediakan tombol **Debitur (Random)** untuk mengisi akun demo debitur secara acak. Akun dan password di atas hanya untuk pengujian aplikasi.

---

## 🚀 Cara Menjalankan Aplikasi (Dua Cara)

### Cara 1: Menggunakan Tombol "JALANKAN_APLIKASI.bat" (Sangat Mudah & Anti-Bentrok Port)
1. Cukup **Double-Click** (klik dua kali) file **`JALANKAN_APLIKASI.bat`** yang ada di folder utama project ini.
2. Sistem akan **secara otomatis mencari port yang masih kosong/bebas** (misalnya port `8080`, `8081`, dst.) agar **TIDAK BENTROK** dengan aplikasi lain di laptop Anda.
3. Browser akan **otomatis terbuka** menampilkan aplikasi web SPK KPR SMART.

### Cara 2: Menjalankan Manual via Terminal
1. Buka Terminal / Command Prompt di folder project:
   ```bash
   cd "d:\PROJECT TEMPLATE JUAL\APLIKASI ASEP"
   ```
2. Jalankan perintah server:
   ```bash
   php artisan serve --port=8080
   ```
3. Buka browser di: `http://127.0.0.1:8080`

---

## 📊 Kriteria Evaluasi SMART

| Kode | Kriteria Evaluasi | Tipe | Bobot ($W_j$) |
| :---: | :--- | :---: | :---: |
| **C1** | Penghasilan Total Nasabah | Benefit | 30.00% |
| **C2** | Lama Bekerja (Tahun) | Benefit | 20.00% |
| **C3** | Rasio Cicilan terhadap Penghasilan (DTI) | Cost | 20.00% |
| **C4** | Status Pekerjaan | Benefit | 15.00% |
| **C5** | Riwayat Kredit SLIK / BI Checking | Benefit | 15.00% |

---

## 📁 Arsitektur Project

- `app/Services/SmartService.php` : Core Engine Kalkulasi SMART Method
- `app/Services/ReportService.php` : Generator Laporan PDF Resmi dengan DomPDF
- `app/Services/UploadService.php` : Service Upload & Preview Dokumen
- `database/seeders/DatabaseSeeder.php` : Seeder Data Master & Akun Demo
- `resources/views/reports/submission_pdf.blade.php` : Template Surat Keputusan KPR ber-QR Code & Tanda Tangan
