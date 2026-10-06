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
    }
}
