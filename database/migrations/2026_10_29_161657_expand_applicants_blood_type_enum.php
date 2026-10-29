<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // ENUM alter must be raw SQL in MySQL
        DB::statement("
            ALTER TABLE applicants
            MODIFY COLUMN blood_type ENUM(
                'A', 'B', 'AB', 'O',
                'A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-'
            ) NULL
        ");
    }

    public function down(): void
    {
        // Strip Rh factor before shrinking enum so existing rows don't break
        DB::table('applicants')
            ->whereNotNull('blood_type')
            ->orderBy('id')
            ->chunkById(200, function ($rows) {
                foreach ($rows as $row) {
                    $plain = strtoupper(rtrim((string) $row->blood_type, '+-'));
                    if (! in_array($plain, ['A', 'B', 'AB', 'O'], true)) {
                        $plain = null;
                    }
                    DB::table('applicants')
                        ->where('id', $row->id)
                        ->update(['blood_type' => $plain]);
                }
            });

        DB::statement("
            ALTER TABLE applicants
            MODIFY COLUMN blood_type ENUM('A', 'B', 'AB', 'O') NULL
        ");
    }
};