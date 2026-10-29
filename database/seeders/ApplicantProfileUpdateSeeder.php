<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\Applicant;
use App\Models\ApplicantProfileUpdate;
use Carbon\Carbon;

class ApplicantProfileUpdateSeeder extends Seeder
{
    /**
     * Run the database seeds for Profile Update Requests.
     */
    public function run(): void
    {
        $applicants = Applicant::take(10)->get();

        if ($applicants->isEmpty()) {
            $this->command->warn('No applicants found in database.');
            return;
        }

        // 1. Pending Update Request: Contact details & address change
        if (isset($applicants[0])) {
            $applicant = $applicants[0];
            $currentData = [
                'mobile_number' => $applicant->mobile_number ?? '09171234567',
                'telephone_number' => $applicant->telephone_number ?? '02-81234567',
                'address' => $applicant->address ?? '123 Old Street, Manila',
                'city' => $applicant->city ?? 'Manila',
                'province' => $applicant->province ?? 'Metro Manila',
                'zip_code' => $applicant->zip_code ?? '1000',
            ];

            $proposedData = [
                'mobile_number' => '09198765432',
                'telephone_number' => '02-88889999',
                'address' => '456 Katipunan Ave, Loyola Heights',
                'city' => 'Quezon City',
                'province' => 'Metro Manila',
                'zip_code' => '1108',
            ];

            $this->createProfileRequest($applicant->id, [
                'status' => 'pending',
                'current_data' => $currentData,
                'proposed_changes' => $proposedData,
                'remarks' => 'Moved to a new residence in Quezon City and updated primary mobile number.',
                'created_at' => Carbon::now()->subHours(3),
            ]);
        }

        // 2. Pending Update Request: Civil status & emergency contact update
        if (isset($applicants[1])) {
            $applicant = $applicants[1];
            $currentData = [
                'civil_status' => $applicant->civil_status ?? 'Single',
                'emergency_contact_name' => $applicant->emergency_contact_name ?? 'Maria Dela Cruz',
                'emergency_contact_number' => $applicant->emergency_contact_number ?? '09180001111',
                'emergency_contact_relationship' => $applicant->emergency_contact_relationship ?? 'Mother',
            ];

            $proposedData = [
                'civil_status' => 'Married',
                'emergency_contact_name' => 'Roberto Santos',
                'emergency_contact_number' => '09205554433',
                'emergency_contact_relationship' => 'Spouse',
            ];

            $this->createProfileRequest($applicant->id, [
                'status' => 'pending',
                'current_data' => $currentData,
                'proposed_changes' => $proposedData,
                'remarks' => 'Recently married, updating civil status and emergency contact to spouse.',
                'created_at' => Carbon::now()->subHours(12),
            ]);
        }

        // 3. Pending Update Request: Name spelling correction & email update
        if (isset($applicants[2])) {
            $applicant = $applicants[2];
            $currentData = [
                'middle_name' => $applicant->middle_name ?? 'Samanon',
                'email' => $applicant->email ?? 'samanon@portal.test',
            ];

            $proposedData = [
                'middle_name' => 'Samañon',
                'email' => 'holden.samanon.2026@gmail.com',
            ];

            $this->createProfileRequest($applicant->id, [
                'status' => 'pending',
                'current_data' => $currentData,
                'proposed_changes' => $proposedData,
                'remarks' => 'Correction of middle name special character (ñ) and update to main personal email.',
                'created_at' => Carbon::now()->subDays(1),
            ]);
        }

        // 4. Approved Update Request (Historical record)
        if (isset($applicants[3])) {
            $applicant = $applicants[3];
            $currentData = ['mobile_number' => '09151112222'];
            $proposedData = ['mobile_number' => '09179998888'];

            $this->createProfileRequest($applicant->id, [
                'status' => 'approved',
                'current_data' => $currentData,
                'proposed_changes' => $proposedData,
                'remarks' => 'Mobile number lost, updating to new SIM number.',
                'reviewed_at' => Carbon::now()->subDays(2),
                'created_at' => Carbon::now()->subDays(3),
            ]);
        }

        // 5. Declined Update Request (Historical record)
        if (isset($applicants[4])) {
            $applicant = $applicants[4];
            $currentData = [
                'first_name' => $applicant->first_name ?? 'John',
                'last_name' => $applicant->last_name ?? 'Doe',
            ];

            $proposedData = [
                'first_name' => 'Jonathan',
                'last_name' => 'Doe-Smith',
            ];

            $this->createProfileRequest($applicant->id, [
                'status' => 'declined',
                'current_data' => $currentData,
                'proposed_changes' => $proposedData,
                'decline_reason' => 'Official government ID or legal document for surname change was not attached.',
                'remarks' => 'Requesting legal name modification.',
                'reviewed_at' => Carbon::now()->subDays(1),
                'created_at' => Carbon::now()->subDays(2),
            ]);
        }

        $this->command->info('Applicant profile update requests seeded successfully.');
    }

    private function createProfileRequest(int $applicantId, array $data): void
    {
        if (class_exists(ApplicantProfileUpdate::class)) {
            ApplicantProfileUpdate::updateOrCreate(
                [
                    'applicant_id' => $applicantId,
                    'status' => $data['status'],
                ],
                [
                    'current_data' => json_encode($data['current_data'] ?? []),
                    'proposed_changes' => json_encode($data['proposed_changes'] ?? []),
                    'changes' => json_encode($data['proposed_changes'] ?? []),
                    'decline_reason' => $data['decline_reason'] ?? null,
                    'remarks' => $data['remarks'] ?? null,
                    'reviewed_at' => $data['reviewed_at'] ?? null,
                    'created_at' => $data['created_at'] ?? Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        } else {
            DB::table('applicant_profile_updates')->insert([
                'applicant_id' => $applicantId,
                'status' => $data['status'],
                'current_data' => json_encode($data['current_data'] ?? []),
                'proposed_changes' => json_encode($data['proposed_changes'] ?? []),
                'decline_reason' => $data['decline_reason'] ?? null,
                'created_at' => $data['created_at'] ?? Carbon::now(),
                'updated_at' => Carbon::now(),
            ]);
        }
    }
}