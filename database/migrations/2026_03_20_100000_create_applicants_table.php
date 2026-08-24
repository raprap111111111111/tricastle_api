<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('applicants', function (Blueprint $table) {
            $table->id();
            $table->string('applicant_code')->unique();  // TC-2025-0001

            // ─── Personal Info ───────────────────────
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('suffix')->nullable();
            $table->string('email')->unique();
            $table->string('phone')->nullable();
            $table->string('mobile')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->enum('gender', ['male', 'female'])->nullable();
            $table->enum('civil_status', [
                'single', 'married', 'widowed', 'separated', 'divorced'
            ])->nullable();
            $table->unsignedTinyInteger('number_of_children')->default(0);
            $table->string('nationality')->default('Filipino');

            // ─── Physical ────────────────────────────
            $table->decimal('height_cm', 5, 2)->nullable();
            $table->decimal('weight_kg', 5, 2)->nullable();
            $table->enum('dominant_hand', ['left', 'right', 'both'])->nullable();
            $table->enum('blood_type', ['A', 'B', 'AB', 'O'])->nullable();

            // ─── Address ─────────────────────────────
            $table->text('current_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('postal_code')->nullable();

            // ─── Passport / Identity ─────────────────
            $table->string('passport_number')->nullable();
            $table->date('passport_expiry')->nullable();
            $table->string('sss_number')->nullable();
            $table->string('tin_number')->nullable();
            $table->string('philhealth_number')->nullable();
            $table->string('pagibig_number')->nullable();

            // ─── Application Status ──────────────────
            $table->enum('status', [
                'pending',        // Just created
                'under_review',   // Staff is reviewing
                'verified',       // Info/docs verified
                'incomplete',     // Missing requirements
                'final_list',     // Approved → ready for batch assignment
                'rejected',       // Not qualified
            ])->default('pending');

            $table->text('rejection_reason')->nullable();
            $table->timestamp('final_listed_at')->nullable();   // When moved to final list
            $table->timestamp('rejected_at')->nullable();

            // ─── Quality Scoring ─────────────────────
            $table->decimal('quality_score', 5, 2)->default(0);
            $table->string('quality_grade')->default('F');

            // ─── Staff Assignment ────────────────────
            $table->foreignId('assigned_staff_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('reviewed_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->foreignId('created_by')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('quality_grade');
            $table->index('applicant_code');
            $table->index('passport_expiry');
            $table->index('final_listed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('applicants');
    }
};