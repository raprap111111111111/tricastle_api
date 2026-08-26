<?php

namespace Database\Seeders;

use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Models\ApplicantBatch;
use App\Models\Batch;
use App\Models\Company;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;

class LegacyApplicantsSeeder extends Seeder
{
    public function run(): void
    {
        $filePath = database_path('seeders/data/legacy_applicants.csv');

        if (! file_exists($filePath)) {
            $this->command?->error("❌ CSV file not found at: {$filePath}");
            return;
        }

        if (filesize($filePath) === 0) {
            $this->command?->error("❌ CSV file is empty (0 bytes). Please save/export the data from WPS Office first.");
            return;
        }

        $this->command?->info('🚀 Overwriting legacy applicants with EXACT CSV deployment dates...');

        $file = fopen($filePath, 'r');
        if (! $file) {
            $this->command?->error('❌ Unable to open CSV file.');
            return;
        }

        $rowNum         = 0;
        $imported       = 0;
        $updated        = 0;
        $skipped        = 0;
        $linked         = 0;
        $batchesCreated = 0;
        $firstCode      = null;
        $lastCode       = null;

        DB::disableQueryLog();

        try {
            DB::beginTransaction();

            while (($rawLine = fgets($file)) !== false) {
                $rowNum++;

                // Skip header / instruction rows (first 3 rows)
                if ($rowNum <= 3) {
                    continue;
                }

                $cleanLine = trim($rawLine);
                if ($cleanLine === '') {
                    continue;
                }

                // Sanitize standalone unescaped quotes
                $sanitizedLine = preg_replace('/(?<!^)(?<!,)"(?!,)(?!$)/', '', $cleanLine);
                $cols          = str_getcsv($sanitizedLine, ',');

                // Detect T-Number position & shift
                $tNumber = '';
                $shift   = 0;

                $col0 = trim($cols[0] ?? '');
                $col1 = trim($cols[1] ?? '');

                if (preg_match('/^[A-Z0-9]{5,8}$/i', $col1) && ! str_contains(strtoupper($col1), 'NUMBER')) {
                    $tNumber = strtoupper($col1);
                    $shift   = 1; // Leading comma
                } elseif (preg_match('/^[A-Z0-9]{5,8}$/i', $col0) && ! str_contains(strtoupper($col0), 'NUMBER')) {
                    $tNumber = strtoupper($col0);
                    $shift   = 0; // No leading comma
                }

                if (empty($tNumber) || str_contains($tNumber, 'TRICASTLE') || str_contains($tNumber, 'T-NUMBER') || str_contains($tNumber, 'UNDECIDED')) {
                    $skipped++;
                    continue;
                }

                // Resolve Name
                $rawName = trim($cols[$shift + 1] ?? '');
                if (empty($rawName) || $rawName === $tNumber) {
                    $rawName = trim($cols[$shift + 4] ?? $cols[$shift + 3] ?? $cols[$shift + 2] ?? '');
                }

                if (empty($rawName) || $rawName === $tNumber || str_contains(strtoupper($rawName), 'UNDECIDED')) {
                    $skipped++;
                    continue;
                }

                [$lastName, $firstName, $middleName] = $this->splitName($rawName);

                if (! $firstCode) {
                    $firstCode = $tNumber;
                }
                $lastCode = $tNumber;

                // -------------------------------------------------------------
                // CORRECTED COLUMN MAPPING (Accounting for $shift offset)
                // Col 1 ($shift+0)  : T-NUMBER
                // Col 2 ($shift+1)  : NAME
                // Col 8 ($shift+7)  : DATE OF BIRTH
                // Col 9 ($shift+8)  : GENDER
                // Col 10 ($shift+9) : FIRST BATCH
                // Col 11 ($shift+10): LATEST BATCH
                // Col 13 ($shift+12): LATEST COMPANY NAME
                // -------------------------------------------------------------
                $dob            = $this->parseDate($cols[$shift + 7] ?? null);                   // DOB
                $gender         = $this->mapGender($cols[$shift + 8] ?? null);                   // Gender
                $firstBatchStr  = trim($cols[$shift + 9] ?? '');                                 // First Batch
                $latestBatchStr = trim($cols[$shift + 10] ?? '');                                // Latest Batch
                $companyName    = trim($cols[$shift + 12] ?? $cols[$shift + 11] ?? '');          // Company Name

                $passportNo     = trim($cols[$shift + 26] ?? $cols[$shift + 27] ?? '') ?: null;        // Passport
                $passportExpiry = $this->parseDate($cols[$shift + 27] ?? $cols[$shift + 28] ?? null);  // Expiry

                // 🗓️ REAL DEPLOYMENT DATE (EMPLOYMENT YEAR & EMPLOYMENT MONTH)
                $empYear        = trim($cols[$shift + 28] ?? $cols[$shift + 29] ?? '');
                $empMonth       = trim($cols[$shift + 29] ?? $cols[$shift + 30] ?? '');
                $flightHist     = trim($cols[$shift + 17] ?? $cols[$shift + 18] ?? '');

                $realDeployedAt = $this->parseRealDeploymentDate($empYear, $empMonth, $flightHist);

                $address     = trim($cols[$shift + 31] ?? $cols[$shift + 32] ?? '') ?: null;       
                $height      = $this->toHeight($cols[$shift + 32] ?? $cols[$shift + 33] ?? null);    
                $weight      = $this->toWeight($cols[$shift + 33] ?? $cols[$shift + 34] ?? null);    
                $civilStatus = $this->mapCivilStatus($cols[$shift + 34] ?? $cols[$shift + 35] ?? null);
                $children    = $this->toChildrenCount($cols[$shift + 35] ?? $cols[$shift + 36] ?? null);
                $religion    = trim($cols[$shift + 36] ?? $cols[$shift + 37] ?? '') ?: null;
                $hand        = $this->mapDominantHand($cols[$shift + 37] ?? $cols[$shift + 38] ?? null);
                $salary      = $this->toSalary($cols[$shift + 40] ?? $cols[$shift + 41] ?? null);
                $examScore   = $this->toScore($cols[$shift + 42] ?? $cols[$shift + 43] ?? null);
                $englishPct  = $this->toScore($cols[$shift + 43] ?? $cols[$shift + 44] ?? null);

                $isStaffMember = $this->isStaff($latestBatchStr) || $this->isStaff($firstBatchStr);
                $status        = $isStaffMember ? ApplicantStatus::Verified : ApplicantStatus::FinalList;

                $payload = [
                    'first_name'              => $firstName !== '' ? $firstName : $lastName,
                    'middle_name'             => $middleName !== '' ? $middleName : null,
                    'last_name'               => $lastName,
                    'email'                   => strtolower($tNumber) . '@tricastle.legacy',
                    'date_of_birth'           => $dob,
                    'gender'                  => $gender,
                    'civil_status'            => $civilStatus,
                    'number_of_children'      => $children,
                    'religion'                => $religion,
                    'nationality'             => 'Filipino',
                    'passport_number'         => $passportNo,
                    'passport_expiry'         => $passportExpiry,
                    'current_address'         => $address,
                    'height_cm'               => $height,
                    'weight_kg'               => $weight,
                    'dominant_hand'           => $hand,
                    'current_salary'          => $salary,
                    'current_salary_currency' => 'PHP',
                    'quality_score'           => $examScore ?? 0,
                    'english_proficiency_pct' => $englishPct ?? 0,
                    'status'                  => $status,
                    'willing_to_be_deployed'  => true,
                    'japan_deployment_ready'  => ! $isStaffMember,
                ];

                $applicant = Applicant::withTrashed()->firstOrNew(['applicant_code' => $tNumber]);
                $wasCreated = ! $applicant->exists;

                if ($applicant->trashed()) {
                    $applicant->restore();
                }

                $applicant->fill($payload);
                $applicant->save();

                if ($wasCreated) {
                    $imported++;
                } else {
                    $updated++;
                }

                // Safe Auto-Create Company
                if ($companyName !== '' && class_exists(Company::class) && Schema::hasTable('companies')) {
                    try {
                        $compCode = 'COMP-' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $companyName));
                        if (strlen($compCode) < 6) {
                            $compCode = 'COMP-' . sprintf('%04d', (int) preg_replace('/\D/', '', $companyName) ?: rand(1000, 9999));
                        }

                        Company::firstOrCreate(
                            ['name' => $companyName],
                            [
                                'code'    => $compCode,
                                'country' => 'Japan',
                            ]
                        );
                    } catch (\Throwable $e) {
                        // Suppress company creation error
                    }
                }

                // Handle Batches & REAL Deployment Dates
                $batchesToProcess = array_filter(array_unique([
                    $this->extractBatchNumber($latestBatchStr),
                    $this->extractBatchNumber($firstBatchStr),
                ]));

                if (! $isStaffMember && ! empty($batchesToProcess)) {
                    foreach ($batchesToProcess as $batchNum) {
                        $batch = Batch::withTrashed()->firstOrNew(['batch_number' => $batchNum]);

                        if ($batch->trashed()) {
                            $batch->restore();
                        }

                        if (! $batch->exists) {
                            $batch->name      = "Batch {$batchNum}";
                            $batch->country   = 'Japan';
                            $batch->status    = 'ongoing';
                            $batch->is_active = false;
                            $batchesCreated++;
                        }

                        if ($realDeployedAt) {
                            $batch->deployment_date = Carbon::parse($realDeployedAt)->toDateString();
                        }

                        $batch->save();

                        $applicantBatch = ApplicantBatch::withTrashed()->firstOrNew([
                            'applicant_id' => $applicant->id,
                            'batch_id'     => $batch->id,
                        ]);

                        if ($applicantBatch->trashed()) {
                            $applicantBatch->restore();
                        }

                        $applicantBatch->status             = 'deployed';
                        $applicantBatch->assigned_at        = $realDeployedAt ?? $applicantBatch->assigned_at ?? now();
                        $applicantBatch->deployed_at        = $realDeployedAt;
                        $applicantBatch->deployment_country = 'Japan';
                        $applicantBatch->deployment_company = $companyName ?: null;

                        $applicantBatch->save();

                        $linked++;
                    }
                }

                if ($rowNum % 500 === 0) {
                    $this->command?->info("...processed {$rowNum} lines (Batches: {$batchesCreated})");
                }
            }

            DB::commit();
            fclose($file);

            $this->command?->info("✅ Legacy applicants seed completed!");
            $this->command?->info("📌 Code Range Processed: [ {$firstCode} ] to [ {$lastCode} ]");
            $this->command?->table(
                ['Total CSV Lines', 'Created', 'Updated', 'Skipped', 'Batches Created', 'Batch Links'],
                [[$rowNum, $imported, $updated, $skipped, $batchesCreated, $linked]]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }

            Log::error('LegacyApplicantsSeeder failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            $this->command?->error('❌ Seed failed: ' . $e->getMessage());
            throw $e;
        }
    }

    private function parseRealDeploymentDate(string $year, string $month, string $flightHist = ''): ?string
    {
        $yearInt = (int) preg_replace('/\D/', '', $year);
        if ($yearInt >= 1980 && $yearInt <= 2030) {
            $monthInt = (int) preg_replace('/\D/', '', $month);
            if ($monthInt < 1 || $monthInt > 12) {
                $monthInt = 1;
            }
            return Carbon::createFromDate($yearInt, $monthInt, 1)->format('Y-m-d H:i:s');
        }

        if ($flightHist !== '' && preg_match('/(19|20)\d{2}/', $flightHist, $m)) {
            return Carbon::createFromDate((int) $m[0], 1, 1)->format('Y-m-d H:i:s');
        }

        return null;
    }

    private function extractBatchNumber(string $batchStr): ?int
    {
        $batchStr = trim($batchStr);
        if ($batchStr === '' || $this->isStaff($batchStr)) {
            return null;
        }

        if (preg_match('/#?(\d+)/', $batchStr, $m)) {
            return (int) $m[1];
        }

        return null;
    }

    private function splitName(string $fullName): array
    {
        $fullName = trim($fullName);

        if (str_contains($fullName, ',')) {
            [$last, $rest] = explode(',', $fullName, 2);
            $last  = trim($last);
            $parts = preg_split('/\s+/', trim($rest)) ?: [];
            $first  = array_shift($parts) ?? '';
            $middle = implode(' ', $parts);

            return [$last, $first, $middle];
        }

        $parts  = preg_split('/\s+/', $fullName) ?: [];
        $last   = array_shift($parts) ?? '';
        $first  = array_shift($parts) ?? '';
        $middle = implode(' ', $parts);

        return [$last, $first, $middle];
    }

    private function parseDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_contains($value, '#')) {
            return null;
        }

        try {
            return Carbon::parse($value)->format('Y-m-d');
        } catch (\Throwable) {
            return null;
        }
    }

    private function mapGender(mixed $value): ?string
    {
        $v = strtoupper(trim((string) $value));

        return match ($v) {
            'M', 'MALE' => 'male',
            'F', 'FEMALE' => 'female',
            default => null,
        };
    }

    private function mapCivilStatus(mixed $value): string
    {
        $v = strtolower(trim((string) $value));

        return match (true) {
            str_contains($v, 'married') => 'married',
            str_contains($v, 'widow')   => 'widowed',
            str_contains($v, 'separat') => 'separated',
            str_contains($v, 'divor')   => 'divorced',
            default                     => 'single',
        };
    }

    private function mapDominantHand(mixed $value): ?string
    {
        $v = strtolower(trim((string) $value));

        return match (true) {
            str_contains($v, 'left') => 'left',
            str_contains($v, 'right') => 'right',
            str_contains($v, 'both') || str_contains($v, 'ambi') => 'both',
            default => null,
        };
    }

    private function isStaff(string $value): bool
    {
        return str_contains(strtoupper(trim($value)), 'STAFF');
    }

    private function toDecimal(mixed $value): ?float
    {
        $v = preg_replace('/[^\d.]/', '', (string) $value);
        if ($v === '' || ! is_numeric($v)) {
            return null;
        }

        return (float) $v;
    }

    private function toHeight(mixed $value): ?float
    {
        $v = $this->toDecimal($value);
        if ($v === null || $v < 50 || $v > 250) {
            return null;
        }

        return $v;
    }

    private function toWeight(mixed $value): ?float
    {
        $v = $this->toDecimal($value);
        if ($v === null || $v < 20 || $v > 300) {
            return null;
        }

        return $v;
    }

    private function toScore(mixed $value): ?int
    {
        $v = preg_replace('/\D/', '', (string) $value);
        if ($v === '' || ! is_numeric($v)) {
            return null;
        }

        $num = (int) $v;
        if ($num < 0 || $num > 100) {
            return null;
        }

        return $num;
    }

    private function toChildrenCount(mixed $value): int
    {
        $v = $this->toInt($value);
        if ($v === null || $v < 0 || $v > 30) {
            return 0;
        }

        return $v;
    }

    private function toSalary(mixed $value): ?float
    {
        $v = $this->toDecimal($value);
        if ($v === null || $v < 0 || $v > 999999) {
            return null;
        }

        return $v;
    }

    private function toInt(mixed $value): ?int
    {
        $v = trim((string) $value);
        if ($v === '' || ! is_numeric($v)) {
            return null;
        }

        return (int) $v;
    }
}