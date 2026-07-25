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
        Setting::setKey('smart_threshold', '80.00');
        Setting::setKey('bank_name', 'BANK KPR SEJAHTERA INDONESIA');
        Setting::setKey('bank_address', 'Jl. Jenderal Sudirman No. 88, Kaveling 5, Jakarta Selatan');
        Setting::setKey('bank_phone', '(021) 555-8888');

        // 2. Seed Admin & Manager
        $admin = User::create([
            'name' => 'Administrator Bank',
            'email' => 'admin@bank.com',
            'role' => 'admin',
            'phone' => '081234567890',
            'password' => Hash::make('password'),
        ]);

        $manager = User::create([
            'name' => 'Anisa Kencana (Manager KPR)',
            'email' => 'manager@bank.com',
            'role' => 'manager',
            'phone' => '081234567891',
            'password' => Hash::make('password'),
        ]);

        // 3. Seed Master Criteria & SubCriteria
        $c1 = Criterion::create(['code' => 'C1', 'name' => 'Penghasilan Total', 'type' => 'benefit', 'weight' => 30.00]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => '> Rp10.000.000', 'operator' => '>', 'min_val' => 10000000, 'utility_value' => 100]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Rp8.000.000 – Rp10.000.000', 'operator' => 'between', 'min_val' => 8000000, 'max_val' => 10000000, 'utility_value' => 90]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Rp6.000.000 – Rp8.000.000', 'operator' => 'between', 'min_val' => 6000000, 'max_val' => 8000000, 'utility_value' => 80]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => 'Rp4.000.000 – Rp6.000.000', 'operator' => 'between', 'min_val' => 4000000, 'max_val' => 6000000, 'utility_value' => 70]);
        SubCriterion::create(['criterion_id' => $c1->id, 'name' => '< Rp4.000.000', 'operator' => '<', 'min_val' => 4000000, 'utility_value' => 50]);

        $c2 = Criterion::create(['code' => 'C2', 'name' => 'Lama Bekerja', 'type' => 'benefit', 'weight' => 20.00]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '> 5 Tahun', 'operator' => '>', 'min_val' => 5.0, 'utility_value' => 100]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '3 – 5 Tahun', 'operator' => 'between', 'min_val' => 3.0, 'max_val' => 5.0, 'utility_value' => 90]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '1 – 3 Tahun', 'operator' => 'between', 'min_val' => 1.0, 'max_val' => 3.0, 'utility_value' => 75]);
        SubCriterion::create(['criterion_id' => $c2->id, 'name' => '< 1 Tahun', 'operator' => '<', 'min_val' => 1.0, 'utility_value' => 50]);

        $c3 = Criterion::create(['code' => 'C3', 'name' => 'Rasio Cicilan (DTI)', 'type' => 'cost', 'weight' => 20.00]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => '< 20%', 'operator' => '<', 'min_val' => 20, 'utility_value' => 100]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => '20% – 35%', 'operator' => 'between', 'min_val' => 20, 'max_val' => 35, 'utility_value' => 90]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => '35% – 50%', 'operator' => 'between', 'min_val' => 35, 'max_val' => 50, 'utility_value' => 70]);
        SubCriterion::create(['criterion_id' => $c3->id, 'name' => '> 50%', 'operator' => '>', 'min_val' => 50, 'utility_value' => 40]);

        $c4 = Criterion::create(['code' => 'C4', 'name' => 'Status Pekerjaan', 'type' => 'benefit', 'weight' => 15.00]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'PNS/BUMN', 'operator' => 'equals_text', 'text_value' => 'PNS/BUMN', 'utility_value' => 100]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Pegawai Tetap Swasta', 'operator' => 'equals_text', 'text_value' => 'Pegawai Tetap Swasta', 'utility_value' => 90]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Wirausaha', 'operator' => 'equals_text', 'text_value' => 'Wirausaha', 'utility_value' => 80]);
        SubCriterion::create(['criterion_id' => $c4->id, 'name' => 'Pegawai Kontrak', 'operator' => 'equals_text', 'text_value' => 'Pegawai Kontrak', 'utility_value' => 60]);

        $c5 = Criterion::create(['code' => 'C5', 'name' => 'Riwayat Kredit (SLIK)', 'type' => 'benefit', 'weight' => 15.00]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => 'Lancar', 'operator' => 'equals_text', 'text_value' => 'Lancar', 'utility_value' => 100]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => 'Dalam Perhatian', 'operator' => 'equals_text', 'text_value' => 'Dalam Perhatian', 'utility_value' => 70]);
        SubCriterion::create(['criterion_id' => $c5->id, 'name' => 'Tidak Lancar', 'operator' => 'equals_text', 'text_value' => 'Tidak Lancar', 'utility_value' => 30]);

        // 4. Seed Nasabah Accounts & Submissions
        $nasabahData = [
            [
                'name' => 'Andi Pratama',
                'email' => 'nasabah@bank.com',
                'nik' => '3201012005920001',
                'tempat_lahir' => 'Bandung',
                'tanggal_lahir' => '1992-05-20',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Asia Afrika No. 45, Bandung',
                'no_hp' => '081234567801',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 1,
                'pekerjaan' => 'Software Engineer',
                'status_pekerjaan' => 'Pegawai Tetap Swasta',
                'lama_bekerja_bulan' => 60, // 5 tahun
                'penghasilan_bulanan' => 12500000,
                'penghasilan_pasangan' => 5000000,
                'pengeluaran_bulanan' => 6000000,
                'cicilan_lain' => 2000000, // DTI = 2000000 / 17500000 = 11.4% (<20%)
                'riwayat_kredit' => 'Lancar',
                'harga_rumah' => 650000000,
                'uang_muka_dp' => 130000000,
                'nilai_pinjaman' => 520000000,
                'tenor_tahun' => 15,
            ],
            [
                'name' => 'Budi Santoso',
                'email' => 'budi@bank.com',
                'nik' => '3201011508880002',
                'tempat_lahir' => 'Jakarta',
                'tanggal_lahir' => '1988-08-15',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Tebet Raya No. 12, Jakarta Selatan',
                'no_hp' => '081234567802',
                'status_pernikahan' => 'Belum Menikah',
                'jumlah_tanggungan' => 0,
                'pekerjaan' => 'Desainer Grafis Freelance',
                'status_pekerjaan' => 'Pegawai Kontrak',
                'lama_bekerja_bulan' => 18, // 1.5 tahun
                'penghasilan_bulanan' => 7000000,
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 3500000,
                'cicilan_lain' => 3000000, // DTI = 3000000/7000000 = 42.8% (35-50%)
                'riwayat_kredit' => 'Dalam Perhatian',
                'harga_rumah' => 450000000,
                'uang_muka_dp' => 90000000,
                'nilai_pinjaman' => 360000000,
                'tenor_tahun' => 20,
            ],
            [
                'name' => 'Citra Lestari',
                'email' => 'citra@bank.com',
                'nik' => '3201015011950003',
                'tempat_lahir' => 'Surabaya',
                'tanggal_lahir' => '1995-11-10',
                'jenis_kelamin' => 'Perempuan',
                'alamat' => 'Jl. Pemuda No. 88, Surabaya',
                'no_hp' => '081234567803',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 2,
                'pekerjaan' => 'Manager Pemasaran BUMN',
                'status_pekerjaan' => 'PNS/BUMN',
                'lama_bekerja_bulan' => 72, // 6 tahun
                'penghasilan_bulanan' => 15000000,
                'penghasilan_pasangan' => 10000000,
                'pengeluaran_bulanan' => 8000000,
                'cicilan_lain' => 3000000, // DTI = 3000000/25000000 = 12% (<20%)
                'riwayat_kredit' => 'Lancar',
                'harga_rumah' => 950000000,
                'uang_muka_dp' => 200000000,
                'nilai_pinjaman' => 750000000,
                'tenor_tahun' => 15,
            ],
            [
                'name' => 'Deni Saputra',
                'email' => 'deni@bank.com',
                'nik' => '3201010101900004',
                'tempat_lahir' => 'Semarang',
                'tanggal_lahir' => '1990-01-01',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Gajah Mada No. 34, Semarang',
                'no_hp' => '081234567804',
                'status_pernikahan' => 'Belum Menikah',
                'jumlah_tanggungan' => 1,
                'pekerjaan' => 'Staf Operasional Swasta',
                'status_pekerjaan' => 'Pegawai Kontrak',
                'lama_bekerja_bulan' => 8, // < 1 tahun
                'penghasilan_bulanan' => 4500000,
                'penghasilan_pasangan' => 0,
                'pengeluaran_bulanan' => 2500000,
                'cicilan_lain' => 2500000, // DTI = 2500000/4500000 = 55.5% (>50%)
                'riwayat_kredit' => 'Tidak Lancar',
                'harga_rumah' => 350000000,
                'uang_muka_dp' => 70000000,
                'nilai_pinjaman' => 280000000,
                'tenor_tahun' => 25,
            ],
            [
                'name' => 'Eko Wibowo',
                'email' => 'eko@bank.com',
                'nik' => '3201011203850005',
                'tempat_lahir' => 'Yogyakarta',
                'tanggal_lahir' => '1985-03-12',
                'jenis_kelamin' => 'Laki-laki',
                'alamat' => 'Jl. Malioboro No. 99, Yogyakarta',
                'no_hp' => '081234567805',
                'status_pernikahan' => 'Menikah',
                'jumlah_tanggungan' => 2,
                'pekerjaan' => 'Pemilik Usaha Kuliner',
                'status_pekerjaan' => 'Wirausaha',
                'lama_bekerja_bulan' => 96, // 8 tahun
                'penghasilan_bulanan' => 22000000,
                'penghasilan_pasangan' => 8000000,
                'pengeluaran_bulanan' => 10000000,
                'cicilan_lain' => 4000000, // DTI = 4000000/30000000 = 13.3% (<20%)
                'riwayat_kredit' => 'Lancar',
                'harga_rumah' => 1200000000,
                'uang_muka_dp' => 300000000,
                'nilai_pinjaman' => 900000000,
                'tenor_tahun' => 10,
            ],
        ];

        $smartService = new SmartService();
        $counter = 1;

        foreach ($nasabahData as $item) {
            $user = User::create([
                'name' => $item['name'],
                'email' => $item['email'],
                'phone' => $item['no_hp'],
                'role' => 'nasabah',
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
                'status_pengajuan' => 'analyzed',
            ]);

            // Execute SMART calculation engine
            $smartService->analyzeSubmission($submission);
        }
    }
}
