<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicant_employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')
                  ->constrained('applicants')
                  ->cascadeOnDelete();

            $table->string('company_name');
            $table->string('position');
            $table->string('industry')->nullable();
            $table->text('job_description')->nullable();

            $table->date('date_started');
            $table->date('date_ended')->nullable();
            $table->boolean('is_current')->default(false);
            $table->boolean('is_overseas')->default(false);

            $table->string('country')->default('Philippines');
            $table->string('city')->nullable();

            $table->decimal('salary', 12, 2)->nullable();
            $table->string('salary_currency', 3)->default('PHP');
            $table->enum('salary_unit', ['per_day', 'per_month', 'per_year'])->default('per_month');

            $table->text('reason_for_leaving')->nullable();

            $table->timestamps();

            $table->index('applicant_id');
            $table->index('is_current');
            $table->index('is_overseas');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicant_employments');
    }
};