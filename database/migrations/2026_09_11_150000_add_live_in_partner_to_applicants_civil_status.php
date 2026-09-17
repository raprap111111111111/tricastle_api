<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Update civil_status enum
        DB::statement("
            ALTER TABLE applicants 
            MODIFY COLUMN civil_status 
            ENUM('single', 'married', 'widowed', 'separated', 'divorced', 'live_in_partner') 
            NULL
        ");

        // 2. Add foreign key link to passport_issuing_offices
        Schema::table('applicants', function (Blueprint $table) {
            $table->foreignId('passport_issuing_office_id')
                  ->nullable()
                  ->after('passport_expiry')
                  ->constrained('passport_issuing_offices')
                  ->nullOnDelete();
        });
    }

    public function down(): void
    {
        // 1. Drop foreign key column
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropConstrainedForeignId('passport_issuing_office_id');
        });

        // 2. Revert civil_status enum
        DB::statement("
            ALTER TABLE applicants 
            MODIFY COLUMN civil_status 
            ENUM('single', 'married', 'widowed', 'separated', 'divorced') 
            NULL
        ");
    }
};