<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE applicants 
            MODIFY COLUMN status ENUM(
                'self_registered',
                'pending',
                'under_review',
                'verified',
                'incomplete',
                'final_list',
                'rejected'
            ) DEFAULT 'pending'
        ");
    }

    public function down(): void
    {
        // Revert any 'self_registered' statuses to 'pending' before shrinking enum
        DB::table('applicants')
            ->where('status', 'self_registered')
            ->update(['status' => 'pending']);

        DB::statement("
            ALTER TABLE applicants 
            MODIFY COLUMN status ENUM(
                'pending',
                'under_review',
                'verified',
                'incomplete',
                'final_list',
                'rejected'
            ) DEFAULT 'pending'
        ");
    }
};