<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();

            $table->enum('education_level', [
                'elementary',
                'high_school',
                'senior_high',
                'vocational',      // TESDA
                'college',
                'post_graduate',
            ]);

            $table->enum('education_status', [
                'graduate',
                'undergraduate',
                'ongoing',
            ])->default('graduate');

            $table->string('school_name');
            $table->string('course')->nullable();
            $table->year('year_started')->nullable();
            $table->year('year_ended')->nullable();
            $table->string('honors')->nullable();

            $table->timestamps();

            $table->index('applicant_id');
            $table->index('education_level');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_educations');
    }
};