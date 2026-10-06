<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('kpr_submissions', function (Blueprint $table) {
            $table->timestamp('c1_verified_at')->nullable()->after('c1_riwayat_kredit');
            $table->foreignId('c1_verified_by')->nullable()->after('c1_verified_at')
                ->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('kpr_submissions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('c1_verified_by');
            $table->dropColumn('c1_verified_at');
        });
    }
};
