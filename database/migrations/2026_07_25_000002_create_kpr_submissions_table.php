<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('kpr_submissions', function (Blueprint $table) {
            $table->id();
            $table->string('no_pengajuan', 30)->unique();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->decimal('harga_rumah', 15, 2);
            $table->decimal('uang_muka_dp', 15, 2);
            $table->decimal('nilai_pinjaman', 15, 2);
            $table->integer('tenor_tahun');
            $table->enum('status_pengajuan', ['draft', 'pending', 'analyzed', 'approved', 'rejected'])->default('pending');
            $table->enum('status_keputusan', ['DITERIMA', 'TIDAK DITERIMA'])->nullable();
            $table->decimal('final_smart_score', 8, 2)->nullable();
            $table->text('manager_notes')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('kpr_submissions');
    }
};
