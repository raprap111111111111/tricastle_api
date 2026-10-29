<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE applicants MODIFY facebook_url VARCHAR(255) NULL");
        DB::statement("ALTER TABLE applicants MODIFY mobile VARCHAR(50) NULL");
        DB::statement("ALTER TABLE applicants MODIFY phone VARCHAR(50) NULL");
        DB::statement("ALTER TABLE applicants MODIFY emergency_contact_phone VARCHAR(50) NULL");
        DB::statement("ALTER TABLE applicants MODIFY current_address VARCHAR(255) NULL");
        DB::statement("ALTER TABLE applicants MODIFY permanent_address VARCHAR(255) NULL");
        DB::statement("ALTER TABLE applicants MODIFY emergency_contact_address VARCHAR(255) NULL");
        DB::statement("ALTER TABLE applicants MODIFY blood_type VARCHAR(20) NULL");
        DB::statement("ALTER TABLE applicants MODIFY dominant_hand VARCHAR(20) NULL");
        DB::statement("ALTER TABLE applicants MODIFY jlpt_level VARCHAR(20) NULL");
        DB::statement("ALTER TABLE applicants MODIFY gender VARCHAR(20) NULL");
        DB::statement("ALTER TABLE applicants MODIFY civil_status VARCHAR(30) NULL");
        DB::statement("ALTER TABLE applicants MODIFY trade_or_occupation VARCHAR(150) NULL");
        DB::statement("ALTER TABLE applicants MODIFY skill_category VARCHAR(150) NULL");
    }

    public function down(): void
    {
        // No down migration required
    }
};