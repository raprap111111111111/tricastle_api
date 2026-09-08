<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('applicant_internships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('applicant_id')->constrained()->cascadeOnDelete();
            $table->foreignId('batch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('internship_program_id')->nullable()->constrained('internship_programs')->nullOnDelete();

            $table->string('program_type')->default('titp'); // titp | ssw | ssw_transfer
            $table->string('status')->default('draft');     // draft | pending | active | completed | transferred | cancelled
            $table->boolean('is_current')->default(true);

            $table->foreignId('dispatching_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('accepting_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('receiving_company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('previous_internship_id')->nullable()->constrained('applicant_internships')->nullOnDelete();

            $table->string('job_description')->nullable();
            $table->string('place_of_internship')->nullable();
            $table->string('municipality')->nullable();

            $table->date('agreement_date')->nullable();
            $table->date('contract_start')->nullable();
            $table->date('contract_end')->nullable();
            $table->unsignedTinyInteger('contract_years')->nullable();

            $table->unsignedInteger('stipend_amount')->nullable();
            $table->unsignedInteger('meal_allowance_amount')->nullable();

            $table->string('work_days')->nullable();
            $table->string('day_off')->nullable();
            $table->string('time_start')->nullable();
            $table->string('time_end')->nullable();
            $table->string('lunch_break')->nullable();

            $table->text('change_reason')->nullable();
            $table->timestamp('changed_at')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();

            $table->timestamps();
            $table->softDeletes();

            $table->index(['applicant_id', 'is_current']);
            $table->index('status');
        });
    }

    public function down(): void {
        Schema::dropIfExists('applicant_internships');
    }
};