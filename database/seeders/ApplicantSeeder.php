<?php

namespace Database\Seeders;

use App\Models\Applicant;
use App\Models\User;
use Illuminate\Database\Seeder;

class ApplicantSeeder extends Seeder
{
    public function run(): void
    {
        $staff = User::first();

        $applicants = [
            [
                'first_name'          => 'Juan',
                'middle_name'         => 'Santos',
                'last_name'           => 'Dela Cruz',
                'email'               => 'juan.delacruz@example.com',
                'phone'               => '+639171234567',
                'mobile'              => '+639171234567',
                'date_of_birth'       => '1990-05-15',
                'gender'              => 'male',
                'civil_status'        => 'single',
                'number_of_children'  => 0,
                'nationality'         => 'Filipino',

                // Physical
                'height_cm'           => 172.50,
                'weight_kg'           => 68.20,
                'dominant_hand'       => 'right',
                'blood_type'          => 'O',

                // Address
                'current_address'     => '123 Rizal Street, Manila',
                'permanent_address'   => '123 Rizal Street, Manila',
                'city'                => 'Manila',
                'province'            => 'Metro Manila',
                'postal_code'         => '1000',

                // Passport
                'passport_number'     => 'P1234567A',
                'passport_expiry'     => '2028-05-15',
                'sss_number'          => '34-1234567-8',
                'tin_number'          => '123-456-789',

                // Status
                'status'              => 'pending',
                'quality_score'       => 0,
                'quality_grade'       => 'F',

                // Staff
                'assigned_staff_id'   => $staff?->id,
                'created_by'          => $staff?->id,
            ],
            [
                'first_name'          => 'Maria',
                'middle_name'         => null,
                'last_name'           => 'Garcia',
                'email'               => 'maria.garcia@example.com',
                'phone'               => '+639181234567',
                'mobile'              => '+639181234567',
                'date_of_birth'       => '1995-08-22',
                'gender'              => 'female',
                'civil_status'        => 'married',
                'number_of_children'  => 2,
                'nationality'         => 'Filipino',

                // Physical
                'height_cm'           => 160.00,
                'weight_kg'           => 55.00,
                'dominant_hand'       => 'right',
                'blood_type'          => 'A',

                // Address
                'current_address'     => '456 Mabini Avenue, Cebu City',
                'permanent_address'   => '456 Mabini Avenue, Cebu City',
                'city'                => 'Cebu City',
                'province'            => 'Cebu',
                'postal_code'         => '6000',

                // Passport
                'passport_number'     => 'P7654321B',
                'passport_expiry'     => '2027-12-01',

                // Status
                'status'              => 'under_review',
                'quality_score'       => 72.50,
                'quality_grade'       => 'B',

                'assigned_staff_id'   => $staff?->id,
                'created_by'          => $staff?->id,
            ],
            [
                'first_name'          => 'Pedro',
                'middle_name'         => 'Cruz',
                'last_name'           => 'Reyes',
                'email'               => 'pedro.reyes@example.com',
                'phone'               => '+639191234567',
                'mobile'              => '+639191234567',
                'date_of_birth'       => '1988-03-10',
                'gender'              => 'male',
                'civil_status'        => 'married',
                'number_of_children'  => 3,
                'nationality'         => 'Filipino',

                // Physical
                'height_cm'           => 168.00,
                'weight_kg'           => 72.00,
                'dominant_hand'       => 'left',
                'blood_type'          => 'B',

                // Address
                'current_address'     => '789 Bonifacio St, Davao City',
                'permanent_address'   => '789 Bonifacio St, Davao City',
                'city'                => 'Davao City',
                'province'            => 'Davao del Sur',
                'postal_code'         => '8000',

                // Passport
                'passport_number'     => 'P9876543C',
                'passport_expiry'     => '2028-03-10',

                // Status
                'status'              => 'verified',
                'quality_score'       => 90.00,
                'quality_grade'       => 'A',

                'assigned_staff_id'   => $staff?->id,
                'created_by'          => $staff?->id,
            ],
        ];

        foreach ($applicants as $data) {
            Applicant::create($data);
        }

        $this->command->info('✅ Applicants seeded: ' . count($applicants));
    }
}