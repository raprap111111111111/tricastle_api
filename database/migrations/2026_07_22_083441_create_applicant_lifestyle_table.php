<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_lifestyle', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();

            // ─── Current Habits ──────────────────────
            $table->boolean('is_smoking')->default(false);
            $table->boolean('is_drinking_alcohol')->default(false);
            $table->boolean('is_using_drugs')->default(false);

            // ─── Past Habits ─────────────────────────
            $table->boolean('was_smoking')->default(false);
            $table->boolean('was_drinking_alcohol')->default(false);
            $table->boolean('was_using_drugs')->default(false);

            // ─── Frequency Notes ─────────────────────
            $table->string('smoking_frequency')->nullable();
            $table->string('drinking_frequency')->nullable();
            $table->text('drugs_notes')->nullable();

            // ─── Health ──────────────────────────────
            $table->boolean('has_medical_condition')->default(false);
            $table->text('medical_notes')->nullable();
            $table->boolean('has_allergies')->default(false);
            $table->text('allergies_notes')->nullable();

            $table->timestamps();

            $table->unique('applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_lifestyle');
    }
};