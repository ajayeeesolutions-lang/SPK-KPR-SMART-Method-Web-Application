<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('submission_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('kpr_submissions')->onDelete('cascade');
            $table->enum('document_type', ['ktp', 'kk', 'slip_gaji', 'npwp', 'rekening_koran', 'sk_kerja', 'pendukung']);
            $table->string('file_path');
            $table->string('original_name');
            $table->integer('file_size')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('submission_documents');
    }
};
