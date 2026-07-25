<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nasabah_profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('nik', 16)->unique();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->text('alamat');
            $table->string('no_hp', 20);
            $table->enum('status_pernikahan', ['Belum Menikah', 'Menikah', 'Cerai']);
            $table->integer('jumlah_tanggungan')->default(0);
            $table->string('pekerjaan');
            $table->enum('status_pekerjaan', ['PNS/BUMN', 'Pegawai Tetap Swasta', 'Wirausaha', 'Pegawai Kontrak', 'Lainnya']);
            $table->integer('lama_bekerja_bulan')->comment('Lama bekerja dalam bulan');
            $table->decimal('penghasilan_bulanan', 15, 2);
            $table->decimal('penghasilan_pasangan', 15, 2)->default(0);
            $table->decimal('pengeluaran_bulanan', 15, 2);
            $table->decimal('cicilan_lain', 15, 2)->default(0);
            $table->enum('riwayat_kredit', ['Lancar', 'Dalam Perhatian', 'Tidak Lancar'])->default('Lancar');
            $table->string('foto_path')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nasabah_profiles');
    }
};
