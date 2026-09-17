<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (! Schema::hasColumn('applicants', 'passport_issuing_office_id')) {
                $table->foreignId('passport_issuing_office_id')
                    ->nullable()
                    ->after('passport_number')
                    ->constrained('passport_issuing_offices')
                    ->nullOnDelete();
            }

            if (! Schema::hasColumn('applicants', 'passport_issue_date')) {
                $table->date('passport_issue_date')
                    ->nullable()
                    ->after('passport_issuing_office_id');
            }

            if (! Schema::hasColumn('applicants', 'passport_received_at')) {
                $table->date('passport_received_at')
                    ->nullable()
                    ->after('passport_issue_date');
            }

            if (! Schema::hasColumn('applicants', 'passport_expiration_date')) {
                $table->date('passport_expiration_date')
                    ->nullable()
                    ->after('passport_received_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            if (Schema::hasColumn('applicants', 'passport_issue_date')) {
                $table->dropColumn('passport_issue_date');
            }
            if (Schema::hasColumn('applicants', 'passport_received_at')) {
                $table->dropColumn('passport_received_at');
            }
            if (Schema::hasColumn('applicants', 'passport_expiration_date')) {
                $table->dropColumn('passport_expiration_date');
            }
        });
    }
};