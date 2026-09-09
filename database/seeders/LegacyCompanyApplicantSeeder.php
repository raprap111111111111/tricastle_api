<?php

namespace Database\Seeders;

use App\Enums\InternshipProgramType;
use App\Models\Applicant;
use App\Models\ApplicantInternship;
use App\Models\Company;
use App\Models\CompanyCategory;
use App\Models\InternshipProgram;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class LegacyCompanyApplicantSeeder extends Seeder
{
    private const DEFAULT_CATEGORY_NAME   = 'General Construction';
    private const DISPATCHING_AGENCY_NAME = 'Tricastle International Inc.';

    public function run(): void
    {
        $csvPath = public_path('legacy_company_applicants.csv');

        if (! file_exists($csvPath)) {
            $csvPath = public_path('legacy_applicant_company_withcateg.csv');
        }

        if (! file_exists($csvPath)) {
            $this->command->error("❌ CSV file not found at: {$csvPath}");
            return;
        }

        $this->command->info("📄 Reading CSV: {$csvPath}...");

        $handle = fopen($csvPath, 'r');
        if (! $handle) {
            $this->command->error('❌ Failed to open CSV file.');
            return;
        }

        // 🎯 1. Ensure Default Fallback Category exists
        $defaultCategory = CompanyCategory::firstOrCreate(
            ['name' => self::DEFAULT_CATEGORY_NAME],
            [
                'slug'      => Str::slug(self::DEFAULT_CATEGORY_NAME),
                'is_active' => true,
            ]
        );

        // 🎯 2. Ensure Sending / Dispatching Agency exists (Philippines side)
        $dispatchingCompany = Company::firstOrCreate(
            ['name' => self::DISPATCHING_AGENCY_NAME],
            [
                'code'        => 'CMP-TRICASTLE',
                'category_id' => $defaultCategory->id,
                'country'     => 'Philippines',
                'is_active'   => true,
            ]
        );

        // Determine default Program Type enum if available
        $defaultProgramType = class_exists(InternshipProgramType::class) && count(InternshipProgramType::cases()) > 0
            ? InternshipProgramType::cases()[0]
            : null;

        $categoriesCreated   = 0;
        $companiesCreated    = 0;
        $programsCreated     = 0;
        $applicantsMatched   = 0;
        $internshipsLinked   = 0;
        $applicantsNotFound  = 0;

        while (($row = fgetcsv($handle)) !== false) {
            // Index 0 = NAME, Index 1 = COMPANY, Index 2 = CATEGORY
            $fullName     = trim($row[0] ?? '');
            $companyName  = trim($row[1] ?? '');
            $categoryName = trim($row[2] ?? '');

            // Skip invalid or header rows
            if (
                empty($fullName) 
                || empty($companyName) 
                || strtoupper($fullName) === 'NAME' 
                || strtoupper($companyName) === 'COMPANY'
                || str_contains($fullName, 'GROUP')
                || str_contains($fullName, 'B46')
            ) {
                continue;
            }

            // 🎯 3. Find or Create Company Category
            if (! empty($categoryName) && strtoupper($categoryName) !== 'CATEGORY') {
                $category = CompanyCategory::firstOrCreate(
                    ['name' => $categoryName],
                    [
                        'slug'      => Str::slug($categoryName),
                        'is_active' => true,
                    ]
                );

                if ($category->wasRecentlyCreated) {
                    $categoriesCreated++;
                }
            } else {
                $category = $defaultCategory;
            }

            // 🎯 4. Find or Create Japanese Accepting/Receiving Company
            $company = Company::where('name', $companyName)->first();

            if (! $company) {
                $company = Company::create([
                    'name'        => $companyName,
                    'code'        => $this->generateUniqueCompanyCode($companyName),
                    'category_id' => $category->id,
                    'country'     => 'Japan',
                    'is_active'   => true,
                ]);
                $companiesCreated++;
            } elseif (! $company->category_id) {
                $company->update(['category_id' => $category->id]);
            }

            // 🎯 5. Find or Create Internship Program for this Company
            $program = InternshipProgram::firstOrCreate(
                [
                    'accepting_company_id'   => $company->id,
                    'dispatching_company_id' => $dispatchingCompany->id,
                ],
                [
                    'name'                         => "{$company->name} Technical Program",
                    'code'                         => 'PRG-' . strtoupper(Str::random(6)),
                    'program_type'                 => $defaultProgramType,
                    'default_receiving_company_id' => $company->id,
                    'contract_years'               => 3,
                    'is_active'                    => true,
                ]
            );

            if ($program->wasRecentlyCreated) {
                $programsCreated++;
            }

            // 🎯 6. Find Applicant
            $applicant = $this->findApplicant($fullName);

            if ($applicant) {
                // Update Applicant's trade_or_occupation and skill_category enum
                $applicant->update([
                    'trade_or_occupation' => $applicant->trade_or_occupation ?: $categoryName,
                    'skill_category'      => $applicant->skill_category ?: 'skilled',
                ]);

                // 🎯 7. Link Applicant to Company & Program via ApplicantInternship
                $internship = ApplicantInternship::firstOrCreate(
                    [
                        'applicant_id'          => $applicant->id,
                        'receiving_company_id'  => $company->id,
                    ],
                    [
                        'internship_program_id' => $program->id,
                        'is_current'            => true,
                    ]
                );

                if ($internship->wasRecentlyCreated) {
                    $internshipsLinked++;
                }

                $applicantsMatched++;
            } else {
                $this->command->warn("⚠️  Applicant not found in DB: {$fullName}");
                $applicantsNotFound++;
            }
        }

        fclose($handle);

        $this->command->info('✅ Seeding completed!');
        $this->command->line("   ├── Categories created:          {$categoriesCreated}");
        $this->command->line("   ├── Companies created:           {$companiesCreated}");
        $this->command->line("   ├── Programs created:            {$programsCreated}");
        $this->command->line("   ├── Applicants matched:          {$applicantsMatched}");
        $this->command->line("   ├── Internships linked:          {$internshipsLinked}");
        $this->command->line("   └── Applicants not found in DB:  {$applicantsNotFound}");
    }

    /**
     * Generate unique company code (e.g., CMP-ARAIKOGY or CMP-ARAIKOGY-1)
     */
    private function generateUniqueCompanyCode(string $name): string
    {
        $cleanName = strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $name));
        $baseCode  = 'CMP-' . substr($cleanName ?: Str::random(6), 0, 8);

        $code    = $baseCode;
        $counter = 1;

        while (Company::withTrashed()->where('code', $code)->exists()) {
            $code = $baseCode . '-' . $counter++;
        }

        return $code;
    }

    /**
     * Smart Applicant Lookup supporting "LASTNAME, FIRSTNAME" or "LASTNAME FIRSTNAME"
     */
    private function findApplicant(string $rawName): ?Applicant
    {
        $rawNameClean = str_replace(',', ' ', $rawName);
        $words        = array_values(array_filter(explode(' ', $rawNameClean)));

        if (empty($words)) {
            return null;
        }

        $lastName  = $words[0];
        $firstName = $words[1] ?? '';

        // Match last_name and first_name prefix
        $applicant = Applicant::query()
            ->where('last_name', 'LIKE', $lastName . '%')
            ->where('first_name', 'LIKE', $firstName . '%')
            ->first();

        if ($applicant) {
            return $applicant;
        }

        // Loose match
        return Applicant::query()
            ->whereRaw("LOWER(REPLACE(CONCAT(last_name, ' ', first_name), ',', '')) LIKE ?", [
                '%' . strtolower($lastName . ' ' . $firstName) . '%',
            ])
            ->first();
    }
}