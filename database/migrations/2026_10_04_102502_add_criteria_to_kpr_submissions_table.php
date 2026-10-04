<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('kpr_submissions', function (Blueprint $table) {
            // C1 - Riwayat SLIK OJK (text kategori)
            $table->string('c1_riwayat_kredit')->nullable()->after('tenor_tahun');
            // C2 - Penghasilan Bersih per bulan (angka Rp)
            $table->decimal('c2_penghasilan_bersih', 15, 2)->nullable()->after('c1_riwayat_kredit');
            // C3 - Status Pekerjaan + Lama Pekerjaan (bulan)
            $table->string('c3_status_pekerjaan')->nullable()->after('c2_penghasilan_bersih');
            $table->integer('c3_lama_bekerja_bulan')->nullable()->after('c3_status_pekerjaan');
            // C4 - Usia saat pengajuan (tahun, dihitung dari tanggal lahir)
            $table->integer('c4_usia')->nullable()->after('c3_lama_bekerja_bulan');
            // C5 - Jumlah Tanggungan (orang)
            $table->integer('c5_jumlah_tanggungan')->nullable()->after('c4_usia');
        });
    }

    public function down(): void
    {
        Schema::table('kpr_submissions', function (Blueprint $table) {
            $table->dropColumn([
                'c1_riwayat_kredit',
                'c2_penghasilan_bersih',
                'c3_status_pekerjaan',
                'c3_lama_bekerja_bulan',
                'c4_usia',
                'c5_jumlah_tanggungan',
            ]);
        });
    }
};
