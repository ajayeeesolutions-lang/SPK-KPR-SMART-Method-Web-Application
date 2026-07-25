# 📑 PANDUAN ALUR KERJA LENGKAP (END-TO-END WORKFLOW)
## Sistem Pendukung Keputusan (SPK) Kelayakan KPR - Metode SMART
**Bank KPR Sejahtera Indonesia**

---

## 📌 1. AKUN AKSES & ROLE PENGGUNA

Aplikasi ini memiliki 3 tingkatan hak akses (*Multi-Role RBAC*) untuk menjaga integritas data dan keamanan transaksi perbankan:

| Role Pengguna | Email Login | Password | Tanggung Jawab Utama |
| :--- | :--- | :--- | :--- |
| **Administrator** | `admin@bank.com` | `password` | Kelola Data Master Kriteria, Bobot, Sub-Kriteria, User, Threshold, dan Eksekusi Mesin SMART |
| **Manager KPR** | `manager@bank.com` | `password` | Tinjauan Eksekutif, Verifikasi Hasil SMART, dan Keputusan Final (*Approve / Reject*) |
| **Calon Nasabah** | `nasabah@bank.com` | `password` | Pengisian Profil Biodata, Pengajuan KPR Baru, Upload Dokumen, Tracking Progres & Cetak Sertifikat |

> 💡 **Fitur 1-Click Interactive Login**: Di halaman `/login`, klik tombol chip **[Admin]**, **[Manager]**, atau **[Nasabah]** untuk mengisi form login secara otomatis.

---

## 🔄 2. ALUR KERJA UTAMA SISTEM (WORKFLOW DIAGRAM)

```
[1. NASABAH]
 ├── Registrasi / Login
 ├── Mengisi Profil Biodata & Finansial
 ├── Mengajukan KPR & Upload Dokumen (KTP, KK, Slip Gaji, NPWP, Rek. Koran)
 └── Status Pengajuan: PENDING
      │
      ▼
[2. MESIN SMART (DECISION ENGINE)]
 ├── Tahap 1: Pembacaan Bobot Awal Kriteria (W_j) -> C1:25%, C2:15%, C3:25%, C4:15%, C5:20%
 ├── Tahap 2: Normalisasi Bobot Kriteria (w_j = W_j / Total_W)
 ├── Tahap 3: Penentuan Nilai Utility (u_j) Berdasarkan Matching Sub-Kriteria (0 - 100)
 ├── Tahap 4: Perkalian Skor Terbobot (w_j × u_j)
 ├── Tahap 5: Penjumlahan Skor Akhir & Ranking Kelayakan (Total Score)
 └── Tahap 6: Evaluasi Threshold (Jika Score ≥ 80.00 -> DITERIMA, Jika < 80.00 -> TIDAK DITERIMA)
      │
      ▼
[3. MANAGER KPR]
 ├── Menerima Notifikasi di Executive Approval Queue
 ├── Menelaah Transparansi Matriks Kalkulasi SMART & Poin Alasan Sistem
 └── Memberikan Eksekusi Akhir (APPROVED dengan Catatan / REJECTED dengan Alasan)
      │
      ▼
[4. HASIL & LAPORAN AKHIR]
 └── Nasabah & Admin dapat Mengunduh/Mencetak SERTIFIKAT KPR OFFICIAL PDF ber-QR Code
```

---

## ⚙️ 3. RINCIAN ALUR KERJA TIAP ROLE

### 🏠 A. Alur Kerja Calon Nasabah
1. **Registrasi / Login Portal Nasabah**:
   - Nasabah membuat akun baru di `/register` atau login menggunakan akun demo `nasabah@bank.com`.
2. **Lengkapi Profil Biodata & Finansial**:
   - Mengisi data NIK, Alamat, Pekerjaan, Penghasilan Bulanan, Pengeluaran, Cicilan Lain saat ini, dan Riwayat Kredit/SLIK.
3. **Pengajuan KPR & Kalkulator Plafon**:
   - Memasukkan Harga Rumah impian dan Uang Muka (DP). Sistem secara otomatis menghitung **Nilai Plafon Pinjaman** (`Harga Rumah - DP`).
   - Memilih Tenor Pinjaman (1 s/d 30 Tahun).
4. **Unggah Dokumen Persyaratan**:
   - Mengunggah berkas KTP, Kartu Keluarga (KK), Slip Gaji, NPWP, Rekening Koran 3 Bulan, dan Surat Keterangan Kerja.
5. **Monitoring Progres & Cetak Hasil**:
   - Memantau status pengajuan pada **Timeline Progres 4 Tahap** di Dashboard Nasabah.
   - Mengunduh **Sertifikat Keputusan KPR (Format PDF)** ber-QR Code setelah disetujui Manager.

---

### 🛡️ B. Alur Kerja Administrator
1. **Kelola Master Kriteria ($C_1 - C_5$)**:
   - Menentukan kriteria penilaian kelayakan KPR beserta bobot persentasenya (Total bobot wajib **100%**):
     - **C1: Total Penghasilan Bulanan** (Bobot: 25%)
     - **C2: Masa Kerja / Usia Usaha** (Bobot: 15%)
     - **C3: Debt to Income Ratio (DTI)** (Bobot: 25%)
     - **C4: Status Kepegawaian** (Bobot: 15%)
     - **C5: Riwayat Kredit / SLIK OJK** (Bobot: 20%)
2. **Kelola Sub-Kriteria & Nilai Utility ($u_j$)**:
   - Mengatur aturan pencocokan (*Operator: >, >=, <=, Between, Exact Text*) dan menentukan nilai utility ($u_j \in [0, 100]$).
3. **Pengaturan Threshold System**:
   - Mengubah nilai ambang batas kelayakan (Standar: **80.00**).
4. **Eksekusi Mesin SMART (*SMART Engine*)**:
   - Menjalankan analisis otomatis atau memicu hitung ulang (*re-evaluate*) untuk seluruh pengajuan nasabah.
   - Memeriksa transparansi kalkulasi di **Wizard 6-Tahap SMART**.

---

### 👔 C. Alur Kerja Manager KPR
1. **Executive Dashboard Review**:
   - Melihat grafik tren pengajuan bulanan, persentase keputusan, dan daftar pengajuan yang membutuhkan persetujuan (*Pending Approval*).
2. **Verifikasi Matriks & Hasil Rekomendasi SMART**:
   - Membuka detail pengajuan nasabah, menguji matriks utility, dan membaca poin-poin analisis yang dihasilkan secara otomatis oleh sistem AI/SMART.
3. **Eksekusi Persetujuan Akhir**:
   - **Setujui (Approve)**: Memberikan catatan persetujuan manager untuk menerbitkan Sertifikat KPR resmi.
   - **Tolak (Reject)**: Memberikan alasan penolakan untuk perbaikan atau pengajuan ulang di masa mendatang.

---

## 🧮 4. FORMULA & TAHAPAN PERHITUNGAN METODE SMART

Metode **SMART (Simple Multi Attribute Rating Technique)** melakukan evaluasi dengan 6 langkah matematis transparan:

### Tahap 1: Penentuan Bobot Awal ($W_j$)
Setiap kriteria $j$ diberikan bobot awal $W_j$ sesuai tingkat kepentingannya:
$$\sum_{j=1}^{m} W_j = 100$$

### Tahap 2: Normalisasi Bobot Kriteria ($w_j$)
Bobot dihitung menjadi bobot ternormalisasi yang totalnya bernilai 1.00:
$$w_j = \frac{W_j}{\sum_{k=1}^{m} W_k}$$

### Tahap 3: Penentuan Nilai Utility ($u_j$)
Nilai atribut nasabah diukur menggunakan fungsi utility $u_j(a_i)$ berdasarkan aturan sub-kriteria:
$$u_j(a_i) \in [0, 100]$$

### Tahap 4 & 5: Nilai Terbobot & Skor Akhir ($S_i$)
Skor akhir nasabah $S_i$ dihitung dari penjumlahan perkalian bobot ternormalisasi dengan nilai utility:
$$S_i = \sum_{j=1}^{m} (w_j \cdot u_j(a_i))$$

### Tahap 6: Keputusan Berdasarkan Threshold
$$\text{Status Keputusan} = \begin{cases} \mathbf{DITERIMA}, & \text{jika } S_i \ge \text{Threshold } (80.00) \\ \mathbf{TIDAK\ DITERIMA}, & \text{jika } S_i < \text{Threshold } (80.00) \end{cases}$$

---

## 🚀 5. CARA MENJALANKAN APLIKASI WEB

1. Buka folder aplikasi: `D:\PROJECT TEMPLATE JUAL\APLIKASI ASEP`.
2. Klik ganda file **`JALANKAN_APLIKASI.bat`**.
3. Bat-file akan secara otomatis:
   - Mencari port bebas di laptop Anda (8080, 8081, dst) sehingga **tidak akan bentrok** dengan aplikasi lain.
   - Membuka aplikasi di browser secara otomatis.

---
*&copy; 2026 Bank KPR Sejahtera Indonesia - Decision Support System SMART Method*
