<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;

class ApplicantPortalSeeder extends Seeder
{
    /**
     * Run the database seeds for Portal Accounts.
     */
    public function run(): void
    {
        $applicants = Applicant::all();

        if ($applicants->isEmpty()) {
            $this->command->warn('No applicants found. Please run main ApplicantSeeder first.');
            return;
        }

        $defaultPassword = Hash::make('password');

        foreach ($applicants as $applicant) {
            $email = $applicant->email ?? "applicant_{$applicant->id}@portal.test";

            // Support both Eloquent or direct DB table insertion
            if (class_exists('App\Models\ApplicantPortalAccount')) {
                \App\Models\ApplicantPortalAccount::updateOrCreate(
                    ['applicant_id' => $applicant->id],
                    [
                        'email' => strtolower($email),
                        'password' => $defaultPassword,
                        'is_active' => true,
                    ]
                );
            } else {
                DB::table('applicant_portal_accounts')->updateOrInsert(
                    ['applicant_id' => $applicant->id],
                    [
                        'email' => strtolower($email),
                        'password' => $defaultPassword,
                        'is_active' => true,
                        'updated_at' => now(),
                    ]
                );
            }
        }

        $this->command->info("Seeded portal accounts for {$applicants->count()} applicants (Password: password).");

        // Execute Profile Updates Seeder
        $this->call(ApplicantProfileUpdateSeeder::class);
    }
}