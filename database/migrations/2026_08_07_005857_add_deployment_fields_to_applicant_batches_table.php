<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicant_batches', function (Blueprint $table) {
            // ─── Deployment location ────────────────────────
            $table->string('deployment_country', 100)->nullable()->after('deployed_at');
            $table->string('deployment_company', 200)->nullable()->after('deployment_country');
            $table->string('deployment_position', 150)->nullable()->after('deployment_company');

            // 🚀 Deployment type (1st Time, TITP, SSW-1, SSW-2, Renewal, Other)
            $table->string('deployment_type', 50)->nullable()->after('deployment_position');

            // ─── Contract details ───────────────────────────
            $table->unsignedSmallInteger('contract_duration_months')->nullable()->after('deployment_type');
            $table->date('contract_start_date')->nullable()->after('contract_duration_months');
            $table->date('contract_end_date')->nullable()->after('contract_start_date');

            // ─── Salary ─────────────────────────────────────
            $table->decimal('monthly_salary', 12, 2)->nullable()->after('contract_end_date');
            $table->string('salary_currency', 10)->default('USD')->after('monthly_salary');

            // ─── Travel details ─────────────────────────────
            $table->date('flight_date')->nullable()->after('salary_currency');
            $table->string('visa_type', 100)->nullable()->after('flight_date');

            // ─── Notes & tracking ───────────────────────────
            $table->text('deployment_notes')->nullable()->after('visa_type');
            $table->text('cancellation_reason')->nullable()->after('deployment_notes');
            $table->timestamp('cancelled_at')->nullable()->after('cancellation_reason');
            $table->foreignId('cancelled_by')
                  ->nullable()
                  ->after('cancelled_at')
                  ->constrained('users')
                  ->nullOnDelete();

            // ─── Indexes for fast filtering ─────────────────
            $table->index('deployment_country');
            $table->index('deployment_company');
            $table->index('deployment_type');           // 🚀 Index for type filter
            $table->index(['status', 'deployed_at']);
        });
    }

    public function down(): void
    {
        Schema::table('applicant_batches', function (Blueprint $table) {
            $table->dropForeign(['cancelled_by']);

            $table->dropIndex(['deployment_country']);
            $table->dropIndex(['deployment_company']);
            $table->dropIndex(['deployment_type']);        // 🚀 Drop type index
            $table->dropIndex(['status', 'deployed_at']);

            $table->dropColumn([
                'deployment_country',
                'deployment_company',
                'deployment_position',
                'deployment_type',                         
                'contract_duration_months',
                'contract_start_date',
                'contract_end_date',
                'monthly_salary',
                'salary_currency',
                'flight_date',
                'visa_type',
                'deployment_notes',
                'cancellation_reason',
                'cancelled_at',
                'cancelled_by',
            ]);
        });
    }
};