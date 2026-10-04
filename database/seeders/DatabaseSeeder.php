<?php

namespace Database\Seeders;

use App\Models\Criterion;
use App\Models\KprSubmission;
use App\Models\NasabahProfile;
use App\Models\Setting;
use App\Models\SubCriterion;
use App\Models\User;
use App\Services\SmartService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Settings
        Setting::setKey('smart_threshold', '0.80');
        Setting::setKey('bank_name', 'PT Citra Pasada Properti');
        Setting::setKey('bank_address', 'Jl. Jenderal Sudirman, Jakarta');
        Setting::setKey('bank_phone', '(021) 555-8888');

        // 2. Seed Admin, Marketing & Pimpinan
        $admin = User::create([
            'name' => 'Admin Sistem',
            'email' => 'admin@citra.com',
            'role' => 'admin',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
        ]);

        $marketing = User::create([
            'name' => 'Staf Marketing',
            'email' => 'marketing@citra.com',
            'role' => 'marketing',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
        ]);

        $pimpinan = User::create([
            'name' => 'Pimpinan Perusahaan',
            'email' => 'pimpinan@citra.com',
            'role' => 'pimpinan',
            'phone' => '081234567892',
            'password' => Hash::make('password'),
        ]);

        // 3. Seed Master Criteria & SubCriteria
        // Sesuai Naskah BAB 3 Tabel 3.2
        $c1 = Criterion::create(['code' => 'C1', 'name' => 'Riwayat SLIK OJK',         'type' => 'benefit', 'weight' => 35.00]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Lancar',                  'operator' => 'equals_text', 'text_value' => 'Lancar',                  'utility_value' => 1.0]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Dalam Perhatian Khusus', 'operator' => 'equals_text', 'text_value' => 'Dalam Perhatian Khusus', 'utility_value' => 0.75]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Kurang Lancar',           'operator' => 'equals_text', 'text_value' => 'Kurang Lancar',           'utility_value' => 0.50]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Diragukan',               'operator' => 'equals_text', 'text_value' => 'Diragukan',               'utility_value' => 0.25]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Macet',                   'operator' => 'equals_text', 'text_value' => 'Macet',                   'utility_value' => 0.0]);

        // C2 = Usia (Bobot 15) — sesuai Naskah Tabel 3.2
        $c2 = Criterion::create(['code' => 'C2', 'name' => 'Usia',                      'type' => 'benefit', 'weight' => 15.00]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '21 - 30 Tahun', 'operator' => 'between', 'min_val' => 21, 'max_val' => 30, 'utility_value' => 1.0]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '31 - 40 Tahun', 'operator' => 'between', 'min_val' => 31, 'max_val' => 40, 'utility_value' => 0.75]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '41 - 50 Tahun', 'operator' => 'between', 'min_val' => 41, 'max_val' => 50, 'utility_value' => 0.50]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '> 50 Tahun',    'operator' => '>',       'min_val' => 50,                  'utility_value' => 0.25]);

        // C3 = Penghasilan Bersih (Bobot 25) — sesuai Naskah Tabel 3.2
        $c3 = Criterion::create(['code' => 'C3', 'name' => 'Penghasilan Bersih',        'type' => 'benefit', 'weight' => 25.00]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => '> Rp10.000.000',              'operator' => '>',       'min_val' => 10000000,                    'utility_value' => 1.0]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => 'Rp8.000.000 – Rp10.000.000', 'operator' => 'between', 'min_val' => 8000000, 'max_val' => 10000000, 'utility_value' => 0.8]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => 'Rp6.000.000 – Rp8.000.000',  'operator' => 'between', 'min_val' => 6000000, 'max_val' => 8000000,  'utility_value' => 0.6]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => 'Rp4.000.000 – Rp6.000.000',  'operator' => 'between', 'min_val' => 4000000, 'max_val' => 6000000,  'utility_value' => 0.4]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => '< Rp4.000.000',               'operator' => '<',       'min_val' => 4000000,                     'utility_value' => 0.2]);

        // C4 = Status dan Lama Pekerjaan (Bobot 15) — sesuai Naskah Tabel 3.2 & Tabel 3.5
        // Utility dihitung: nilai/4 karena skala naskah 1-5 → (Cout-Cmin)/(Cmax-Cmin) = (x-1)/(5-1)
        $c4 = Criterion::create(['code' => 'C4', 'name' => 'Status dan Lama Pekerjaan', 'type' => 'benefit', 'weight' => 15.00]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Tetap > 5 Tahun',  'operator' => 'equals_text', 'text_value' => 'Tetap > 5 Tahun',  'utility_value' => 1.0]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Tetap 3-5 Tahun',  'operator' => 'equals_text', 'text_value' => 'Tetap 3-5 Tahun',  'utility_value' => 0.75]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Kontrak',           'operator' => 'equals_text', 'text_value' => 'Kontrak',           'utility_value' => 0.5]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Freelance',         'operator' => 'equals_text', 'text_value' => 'Freelance',         'utility_value' => 0.25]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Tidak Bekerja',     'operator' => 'equals_text', 'text_value' => 'Tidak Bekerja',     'utility_value' => 0.0]);

        // C5 = Jumlah Tanggungan (Bobot 10, Cost) — sesuai Naskah Tabel 3.2
        $c5 = Criterion::create(['code' => 'C5', 'name' => 'Jumlah Tanggungan',         'type' => 'cost',    'weight' => 10.00]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => '0 Tanggungan',     'operator' => 'between', 'min_val' => 0, 'max_val' => 0, 'utility_value' => 1.0]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => '1 - 2 Tanggungan', 'operator' => 'between', 'min_val' => 1, 'max_val' => 2, 'utility_value' => 0.75]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => '3 - 4 Tanggungan', 'operator' => 'between', 'min_val' => 3, 'max_val' => 4, 'utility_value' => 0.50]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => '> 4 Tanggungan',   'operator' => '>',       'min_val' => 4,                'utility_value' => 0.25]);

        // 4. Seed Nasabah Accounts & Submissions
        // Keterangan target skor (estimasi u(ai)):
        // LAYAK (>= 0.80): Nasabah 1-4
        // DIPERTIMBANGKAN (0.60 - 0.79): Nasabah 5-7
        // TIDAK LAYAK (< 0.60): Nasabah 8-9
        // MENUNGGU (pending): Nasabah 10
        $nasabahData = [
            // ===== LAYAK =====
            [
                'name' => 'Achmad fauzi',
                'email' => 'achmad@mail.com',
                'nik' => '3201012005920001',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1992-05-20', // u=0.8 (Usia 30an)
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Asia Afrika No. 45, Bandung',
                'no_hp' => '081234567801',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 1,         // u=0.75 (1-2)
                'pekerjaan' => 'Software Engineer',
                'status_pekerjaan' => 'Tetap > 5 Tahun',   // u=1.0 (60 bulan = >5 tahun)
                'lama_bekerja_bulan' => 60,
                'penghasilan_bulanan' => 12500000, // u=1.0 (>10jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 6000000,
                'cicilan_lain' => 2000000,
                'riwayat_kredit' => 'Lancar',     // u=1.0
                'harga_rumah' => 650000000,
                'uang_muka_dp' => 130000000,
                'nilai_pinjaman' => 520000000,
                'tenor_tahun' => 15,
                'status_pengajuan' => 'analyzed',
            ],
            [
                'name' => 'Aldi saputra',
                'email' => 'aldi@mail.com',
                'nik' => '3201012005980002',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1998-03-15', // u=1.0 (Usia 20an)
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Sudirman No. 10, Jakarta',
                'no_hp' => '081234567802',
                'status_pernikahan' => 'Belum Menikah',
                'jumlah_tanggungan' => 0,         // u=1.0 (0)
                'pekerjaan' => 'PNS',
                'status_pekerjaan' => 'Tetap > 5 Tahun',   // u=1.0 (60 bulan = >5 tahun)
                'lama_bekerja_bulan' => 36,
                'penghasilan_bulanan' => 11000000, // u=1.0 (>10jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 4000000,
                'cicilan_lain' => 0,
                'riwayat_kredit' => 'Lancar',     // u=1.0
                'harga_rumah' => 500000000,
                'uang_muka_dp' => 100000000,
                'nilai_pinjaman' => 400000000,
                'tenor_tahun' => 20,
                'status_pengajuan' => 'analyzed',
            ],
            [
                'name' => 'Johan setiawan',
                'email' => 'johan@mail.com',
                'nik' => '3201012005880003',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1988-07-22', // u=0.8 (Usia 30an)
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Pemuda No. 33, Surabaya',
                'no_hp' => '081234567803',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 2,         // u=0.75 (1-2)
                'pekerjaan' => 'Dokter Spesialis',
                'status_pekerjaan' => 'Tetap > 5 Tahun',   // u=1.0 (60 bulan = >5 tahun)
                'lama_bekerja_bulan' => 84,
                'penghasilan_bulanan' => 18000000, // u=1.0 (>10jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 7000000,
                'cicilan_lain' => 1500000,
                'riwayat_kredit' => 'Lancar',     // u=1.0
                'harga_rumah' => 900000000,
                'uang_muka_dp' => 180000000,
                'nilai_pinjaman' => 720000000,
                'tenor_tahun' => 20,
                'status_pengajuan' => 'analyzed',
            ],
            [
                'name' => 'Eka oktavia',
                'email' => 'eka@mail.com',
                'nik' => '3201012005780004',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1978-11-05', // u=0.6 (Usia 40an)
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Malioboro No. 77, Yogyakarta',
                'no_hp' => '081234567804',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 0,         // u=1.0 (0)
                'pekerjaan' => 'Direktur',
                'status_pekerjaan' => 'Tetap > 5 Tahun',   // u=1.0 (60 bulan = >5 tahun)
                'lama_bekerja_bulan' => 120,
                'penghasilan_bulanan' => 25000000, // u=1.0 (>10jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 8000000,
                'cicilan_lain' => 3000000,
                'riwayat_kredit' => 'Lancar',     // u=1.0
                'harga_rumah' => 1200000000,
                'uang_muka_dp' => 300000000,
                'nilai_pinjaman' => 900000000,
                'tenor_tahun' => 15,
                'status_pengajuan' => 'analyzed',
            ],

            // ===== DIPERTIMBANGKAN =====
            [
                'name' => 'Lina Marlina',
                'email' => 'lina@mail.com',
                'nik' => '3201012005970005',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1997-02-14', // u=1.0 (Usia 20an)
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Diponegoro No. 12, Semarang',
                'no_hp' => '081234567805',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 1,         // u=0.75 (1-2)
                'pekerjaan' => 'Staff Administrasi',
                'status_pekerjaan' => 'Kontrak',               // u=0.5 (Kontrak)
                'lama_bekerja_bulan' => 30,
                'penghasilan_bulanan' => 7000000, // u=0.6 (6-8jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 3500000,
                'cicilan_lain' => 1000000,
                'riwayat_kredit' => 'Dalam Perhatian Khusus', // u=0.75
                'harga_rumah' => 350000000,
                'uang_muka_dp' => 70000000,
                'nilai_pinjaman' => 280000000,
                'tenor_tahun' => 15,
                'status_pengajuan' => 'analyzed',
            ],
            [
                'name' => 'Andi Santoso',
                'email' => 'santoso@mail.com',
                'nik' => '3201012005850006',
                'tempat_lahir' => 'Medan',
                'tanggal_lahir' => '1985-09-30', // u=0.8 (Usia 30an)
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Gatot Subroto No. 55, Medan',
                'no_hp' => '081234567806',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 3,         // u=0.5 (3-4)
                'pekerjaan' => 'Wiraswasta',
                'status_pekerjaan' => 'Freelance',             // u=0.25 (Freelance)
                'lama_bekerja_bulan' => 72,
                'penghasilan_bulanan' => 7500000, // u=0.6 (6-8jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 4000000,
                'cicilan_lain' => 2000000,
                'riwayat_kredit' => 'Lancar',     // u=1.0
                'harga_rumah' => 400000000,
                'uang_muka_dp' => 80000000,
                'nilai_pinjaman' => 320000000,
                'tenor_tahun' => 15,
                'status_pengajuan' => 'analyzed',
            ],
            [
                'name' => 'Rina Gunawan',
                'email' => 'rina@mail.com',
                'nik' => '3201012005900007',
                'tempat_lahir' => 'Palembang',
                'tanggal_lahir' => '1990-06-17', // u=0.8 (Usia 30an)
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Merdeka No. 8, Palembang',
                'no_hp' => '081234567807',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 2,         // u=0.75 (1-2)
                'pekerjaan' => 'Marketing',
                'status_pekerjaan' => 'Kontrak',               // u=0.5 (Kontrak)
                'lama_bekerja_bulan' => 18,
                'penghasilan_bulanan' => 9000000, // u=0.8 (8-10jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 5000000,
                'cicilan_lain' => 1500000,
                'riwayat_kredit' => 'Dalam Perhatian Khusus', // u=0.75
                'harga_rumah' => 450000000,
                'uang_muka_dp' => 90000000,
                'nilai_pinjaman' => 360000000,
                'tenor_tahun' => 15,
                'status_pengajuan' => 'analyzed',
            ],

            // ===== TIDAK LAYAK =====
            [
                'name' => 'Bambang Irawan',
                'email' => 'bambang@mail.com',
                'nik' => '3201012005700008',
                'tempat_lahir' => 'Solo',
                'tanggal_lahir' => '1970-04-10', // u=0.4 (Usia >50)
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Slamet Riyadi No. 21, Solo',
                'no_hp' => '081234567808',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 5,         // u=0.25 (>4)
                'pekerjaan' => 'Pedagang',
                'status_pekerjaan' => 'Freelance',             // u=0.25 (Freelance)
                'lama_bekerja_bulan' => 24,
                'penghasilan_bulanan' => 4500000, // u=0.4 (4-6jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 3500000,
                'cicilan_lain' => 2500000,
                'riwayat_kredit' => 'Diragukan',  // u=0.25
                'harga_rumah' => 250000000,
                'uang_muka_dp' => 50000000,
                'nilai_pinjaman' => 200000000,
                'tenor_tahun' => 10,
                'status_pengajuan' => 'analyzed',
            ],
            [
                'name' => 'Titik Puspa',
                'email' => 'titik@mail.com',
                'nik' => '3201012005680009',
                'tempat_lahir' => 'Makassar',
                'tanggal_lahir' => '1968-12-01', // u=0.4 (Usia >50)
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Pengayoman No. 5, Makassar',
                'no_hp' => '081234567809',
                'status_pernikahan' => 'Cerai',
                'jumlah_tanggungan' => 4,         // u=0.5 (3-4)
                'pekerjaan' => 'Buruh',
                'status_pekerjaan' => 'Tidak Bekerja',         // u=0.0 (tidak bekerja tetap)
                'lama_bekerja_bulan' => 10,
                'penghasilan_bulanan' => 3000000, // u=0.2 (<4jt)
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 2500000,
                'cicilan_lain' => 1000000,
                'riwayat_kredit' => 'Macet',      // u=0.0
                'harga_rumah' => 200000000,
                'uang_muka_dp' => 40000000,
                'nilai_pinjaman' => 160000000,
                'tenor_tahun' => 10,
                'status_pengajuan' => 'analyzed',
            ],

            // ===== MENUNGGU =====
            [
                'name' => 'Rudi Gunawan',
                'email' => 'rudi@mail.com',
                'nik' => '3201012005930010',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1993-08-25',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Braga No. 15, Bandung',
                'no_hp' => '081234567810',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 1,
                'pekerjaan' => 'Akuntan',
                'status_pekerjaan' => 'Tetap 3-5 Tahun',       // u=0.75 (48 bulan = 4 tahun)
                'lama_bekerja_bulan' => 48,
                'penghasilan_bulanan' => 8500000,
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 4000000,
                'cicilan_lain' => 1500000,
                'riwayat_kredit' => 'Lancar',
                'harga_rumah' => 500000000,
                'uang_muka_dp' => 100000000,
                'nilai_pinjaman' => 400000000,
                'tenor_tahun' => 15,
                'status_pengajuan' => 'pending',  // Menunggu
            ],
        ];

        $smartService = new SmartService();
        $counter = 1;

        foreach ($nasabahData as $item) {
            $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'phone' => $item['no_hp'],
                'role' => 'debitur',
                'password' => Hash::make('password'),
            ]);

            $profile = NasabahProfile::create([
                'user_id' => $user->id,
                'nik' => $item['nik'],
                'nama_lengkap' => $item['name'],
                'tempat_lahir' => $item['tempat_lahir'],
                'tanggal_lahir' => $item['tanggal_lahir'],
                'jenis_kelamin' => $item['jenis_kelamin'],
                'alamat' => $item['alamat'],
                'no_hp' => $item['no_hp'],
                'status_pernikahan' => $item['status_pernikahan'],
                'jumlah_tanggungan' => $item['jumlah_tanggungan'],
                'pekerjaan' => $item['pekerjaan'],
                'status_pekerjaan' => $item['status_pekerjaan'],
                'lama_bekerja_bulan' => $item['lama_bekerja_bulan'],
                'penghasilan_bulanan' => $item['penghasilan_bulanan'],
                'penghasilan_pasangan' => $item['penghasilan_pasangan'],
                'pengeluaran_bulanan' => $item['pengeluaran_bulanan'],
                'cicilan_lain' => $item['cicilan_lain'],
                'riwayat_kredit' => $item['riwayat_kredit'],
            ]);

            $noPengajuan = 'NAS-' . date('Y') . '-' . Str::padLeft($counter++, 3, '0');
            
            $submission = KprSubmission::create([
                'no_pengajuan' => $noPengajuan,
                'user_id' => $user->id,
                'harga_rumah' => $item['harga_rumah'],
                'uang_muka_dp' => $item['uang_muka_dp'],
                'nilai_pinjaman' => $item['nilai_pinjaman'],
                'tenor_tahun' => $item['tenor_tahun'],
                'status_pengajuan' => $item['status_pengajuan'],
            ]);

            if ($item['status_pengajuan'] === 'analyzed') {
                try {
                    $smartService->analyzeSubmission($submission);
                } catch (\Exception $e) {}
            }
        }
    }
}
