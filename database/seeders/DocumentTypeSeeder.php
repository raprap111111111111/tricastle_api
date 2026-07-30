<?php

namespace Database\Seeders;

use App\Models\DocumentType;
use Illuminate\Database\Seeder;

class DocumentTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            [
                'name'               => 'Passport',
                'code'               => 'PASSPORT',
                'description'        => 'Valid international passport',
                'required_fields'    => ['passport_number', 'issue_date', 'expiry_date', 'issue_place'],
                'validation_rules'   => ['passport_number' => 'required|string', 'expiry_date' => 'required|date|after:today'],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => 1825,
                'expiry_warning_days'=> 90,
                'category'           => 'primary',
                'sort_order'         => 1,
            ],
            [
                'name'               => 'NBI Clearance',
                'code'               => 'NBI',
                'description'        => 'National Bureau of Investigation Clearance',
                'required_fields'    => ['clearance_number', 'issue_date', 'expiry_date'],
                'validation_rules'   => ['clearance_number' => 'required|string'],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => 365,
                'expiry_warning_days'=> 30,
                'category'           => 'primary',
                'sort_order'         => 2,
            ],
            [
                'name'               => 'Medical Certificate',
                'code'               => 'MEDICAL',
                'description'        => 'Pre-employment medical examination certificate',
                'required_fields'    => ['certificate_number', 'issue_date', 'clinic_name', 'result'],
                'validation_rules'   => ['result' => 'required|in:fit,unfit,conditional'],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => 180,
                'expiry_warning_days'=> 30,
                'category'           => 'primary',
                'sort_order'         => 3,
            ],
            [
                'name'               => 'POEA Contract',
                'code'               => 'POEA_CONTRACT',
                'description'        => 'POEA verified employment contract',
                'required_fields'    => ['contract_number', 'employer_name', 'position', 'salary', 'duration'],
                'validation_rules'   => ['contract_number' => 'required|string'],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => null,
                'expiry_warning_days'=> 30,
                'category'           => 'primary',
                'sort_order'         => 4,
            ],
            [
                'name'               => 'OEC / Exit Clearance',
                'code'               => 'OEC',
                'description'        => 'Overseas Employment Certificate',
                'required_fields'    => ['oec_number', 'issue_date', 'expiry_date'],
                'validation_rules'   => ['oec_number' => 'required|string'],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => 60,
                'expiry_warning_days'=> 14,
                'category'           => 'primary',
                'sort_order'         => 5,
            ],
            [
                'name'               => 'Birth Certificate',
                'code'               => 'BIRTH_CERT',
                'description'        => 'PSA authenticated birth certificate',
                'required_fields'    => ['document_number', 'issue_date'],
                'validation_rules'   => [],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => null,
                'expiry_warning_days'=> 30,
                'category'           => 'supporting',
                'sort_order'         => 6,
            ],
            [
                'name'               => 'TESDA Certificate',
                'code'               => 'TESDA',
                'description'        => 'Technical skills certificate from TESDA',
                'required_fields'    => ['certificate_number', 'course', 'issue_date'],
                'validation_rules'   => [],
                'is_required'        => false,
                'is_active'          => true,
                'validity_days'      => null,
                'expiry_warning_days'=> 30,
                'category'           => 'supporting',
                'sort_order'         => 7,
            ],
            [
                'name'               => 'Visa',
                'code'               => 'VISA',
                'description'        => 'Work visa for destination country',
                'required_fields'    => ['visa_number', 'visa_type', 'issue_date', 'expiry_date', 'country'],
                'validation_rules'   => ['expiry_date' => 'required|date|after:today'],
                'is_required'        => true,
                'is_active'          => true,
                'validity_days'      => 730,
                'expiry_warning_days'=> 60,
                'category'           => 'primary',
                'sort_order'         => 8,
            ],
        ];

        foreach ($types as $type) {
            DocumentType::firstOrCreate(
                ['code' => $type['code']],
                $type
            );
        }

        $this->command->info('✅ Document types seeded: ' . count($types));
    }
}