<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('quality_scores', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained()
                  ->cascadeOnDelete();
            $table->decimal('overall_score', 5, 2)->default(0);
            $table->string('grade')->default('F'); // A, B, C, D, F
            $table->decimal('completeness_score', 5, 2)->default(0);
            $table->decimal('accuracy_score', 5, 2)->default(0);
            $table->decimal('consistency_score', 5, 2)->default(0);
            $table->decimal('timeliness_score', 5, 2)->default(0);
            $table->integer('total_documents')->default(0);
            $table->integer('verified_documents')->default(0);
            $table->integer('rejected_documents')->default(0);
            $table->integer('pending_documents')->default(0);
            $table->integer('total_mismatches')->default(0);
            $table->integer('critical_mismatches')->default(0);
            $table->integer('open_corrections')->default(0);
            $table->json('breakdown')->nullable(); // Detailed breakdown
            $table->timestamp('calculated_at');
            $table->foreignId('calculated_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->timestamps();

            $table->index('grade');
            $table->index('overall_score');
            $table->index('calculated_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('quality_scores');
    }
};