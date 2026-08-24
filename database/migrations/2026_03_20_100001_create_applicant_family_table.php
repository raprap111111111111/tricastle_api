<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_family', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();

            // Spouse Details (Wife / Husband)
            $table->string('spouse_name')->nullable();
            $table->string('spouse_occupation')->nullable();
            $table->decimal('spouse_salary', 12, 2)->nullable();
            $table->string('spouse_salary_unit')->default('per_month'); // per_day, per_month

            // Parents Details
            $table->string('father_name')->nullable();
            $table->string('mother_name')->nullable();

            $table->timestamps();

            $table->unique('applicant_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_family');
    }
};