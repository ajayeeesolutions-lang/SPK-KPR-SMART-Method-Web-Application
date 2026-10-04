<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('smart_analysis_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('submission_id')->constrained('kpr_submissions')->onDelete('cascade');
            $table->json('initial_weights');
            $table->json('normalized_weights');
            $table->json('utilities');
            $table->json('weighted_scores');
            $table->decimal('total_score', 8, 2);
            $table->enum('decision', ['LAYAK', 'DIPERTIMBANGKAN', 'TIDAK LAYAK']);
            $table->json('explanations');
            $table->timestamp('analyzed_at');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('smart_analysis_results');
    }
};
