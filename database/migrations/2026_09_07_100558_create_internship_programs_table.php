<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('internship_programs', function (Blueprint $table) {
            $table->id();

            // ── 1. Identity & Program Classification ─────────────────────────
            $table->string('code')->unique();                         // e.g. TITP-CONST-01, SSW-CONST-01
            $table->string('name');                                   // e.g. Technical Intern Training (Construction)
            $table->string('program_type')->default('titp');          // titp | ssw | ssw_transfer
            $table->string('document_template')->default('default');  // Key for Blade view (e.g. moa.titp, moa.ssw)
            $table->text('description')->nullable();

            // ── 2. Company Transfer Rules ────────────────────────────────────
            $table->boolean('allows_company_transfer')->default(false); // False for TITP, True for SSW

            // ── 3. Company Relationships ─────────────────────────────────────
            $table->foreignId('dispatching_company_id')
                  ->constrained('companies')
                  ->restrictOnDelete();

            // Accepting Organization (MARUCON/AO) is required for TITP, but optional for SSW
            $table->foreignId('accepting_company_id')
                  ->nullable()
                  ->constrained('companies')
                  ->nullOnDelete();

            $table->foreignId('default_receiving_company_id')
                  ->nullable()
                  ->constrained('companies')
                  ->nullOnDelete();

            // ── 4. Contract Defaults ─────────────────────────────────────────
            $table->unsignedTinyInteger('contract_years')->default(3);
            $table->string('default_municipality')->default('Murcia');
            $table->string('default_job_description')->nullable();    // e.g. FRAME WORKING, CAREGIVING
            $table->string('training_type')->nullable();              // e.g. Construction Intern, SSW Worker

            // ── 5. Compensation Defaults ─────────────────────────────────────
            $table->string('compensation_type')->default('allowance'); // allowance (TITP) | salary (SSW)
            $table->unsignedInteger('stipend_amount')->default(40000);
            $table->unsignedInteger('meal_allowance_amount')->default(40000);
            $table->string('compensation_currency', 3)->default('JPY');
            $table->boolean('has_bonus')->default(false);

            // ── 6. Schedule Defaults ─────────────────────────────────────────
            $table->string('work_days')->default('MONDAY to SATURDAY');
            $table->string('day_off')->default('SUNDAY');
            $table->string('time_start')->default('8:00 AM');
            $table->string('time_end')->default('6:00 PM');
            $table->string('lunch_break')->default('40-50 mins.');
            $table->string('overtime_policy')->default('Optional / Variable / Flexible');

            // ── 7. Witness Defaults ──────────────────────────────────────────
            $table->foreignId('witness_user_id')
                  ->nullable()
                  ->constrained('users')
                  ->nullOnDelete();
            $table->string('witness_name')->nullable();
            $table->string('witness_title')->nullable();
            $table->string('witness_org')->nullable();

            // ── 8. Status & Timestamps ───────────────────────────────────────
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();

            // ── 9. Indexes ───────────────────────────────────────────────────
            $table->index('code');
            $table->index('program_type');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('internship_programs');
    }
};