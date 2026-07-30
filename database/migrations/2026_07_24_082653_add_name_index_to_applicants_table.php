<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            // Speeds up "find by name" duplicate checks
            $table->index(['first_name', 'last_name'], 'applicants_name_index');

            // Passport unique-ish check (nullable-safe)
            $table->index('passport_number', 'applicants_passport_index');
        });
    }

    public function down(): void
    {
        Schema::table('applicants', function (Blueprint $table) {
            $table->dropIndex('applicants_name_index');
            $table->dropIndex('applicants_passport_index');
        });
    }
};