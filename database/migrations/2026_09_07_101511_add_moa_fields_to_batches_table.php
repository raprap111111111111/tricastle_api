<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::table('batches', function (Blueprint $table) {
            $table->foreignId('internship_program_id')->nullable()->after('id')->constrained('internship_programs')->nullOnDelete();
            $table->foreignId('receiving_company_id')->nullable()->after('internship_program_id')->constrained('companies')->nullOnDelete();
            $table->foreignId('accepting_company_id')->nullable()->after('receiving_company_id')->constrained('companies')->nullOnDelete();

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

            $table->string('default_job_description')->nullable();
            $table->string('place_of_internship_override')->nullable();
            $table->string('municipality')->nullable();
        });
    }

    public function down(): void {
        Schema::table('batches', function (Blueprint $table) {
            $table->dropForeign(['internship_program_id']);
            $table->dropForeign(['receiving_company_id']);
            $table->dropForeign(['accepting_company_id']);
            $table->dropColumn([
                'internship_program_id', 'receiving_company_id', 'accepting_company_id',
                'contract_start', 'contract_end', 'contract_years', 'stipend_amount',
                'meal_allowance_amount', 'work_days', 'day_off', 'time_start', 'time_end',
                'lunch_break', 'default_job_description', 'place_of_internship_override', 'municipality',
            ]);
        });
    }
};