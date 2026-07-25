<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sub_criteria', function (Blueprint $table) {
            $table->id();
            $table->foreignId('criterion_id')->constrained('criteria')->onDelete('cascade');
            $table->string('name');
            $table->enum('operator', ['>', '>=', '<', '<=', '=', 'between', 'equals_text'])->default('>=');
            $table->decimal('min_val', 15, 2)->nullable();
            $table->decimal('max_val', 15, 2)->nullable();
            $table->string('text_value')->nullable();
            $table->decimal('utility_value', 8, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sub_criteria');
    }
};
