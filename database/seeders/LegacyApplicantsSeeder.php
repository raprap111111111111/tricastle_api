<?php

namespace Database\Seeders;

use App\Enums\ApplicantStatus;
use App\Enums\BatchStatus;
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
            $this->command?->error("❌ CSV file is empty (0 bytes).");
            return;
        }

        $file = fopen($filePath, 'r');
        if (! $file) {
            $this->command?->error('❌ Unable to open CSV file.');
            return;
        }

        $this->command?->info("🚀 Loading pre-fetched database lookups into memory...");

        // -------------------------------------------------------------
        // 🚀 OPTIMIZATION 1: Pre-fetch existing records into HashMaps
        // -------------------------------------------------------------
        $existingApplicants = Applicant::withTrashed()->pluck('id', 'applicant_code')->toArray();
        $existingBatches    = Batch::withTrashed()->pluck('id', 'batch_number')->toArray();
        $existingCompanies  = class_exists(Company::class) && Schema::hasTable('companies')
            ? Company::pluck('id', 'name')->toArray()
            : [];

        // Pre-fetch links into key "applicantId_batchId"
        $existingLinks = ApplicantBatch::withTrashed()
            ->select('id', 'applicant_id', 'batch_id')
            ->get()
            ->keyBy(fn ($i) => "{$i->applicant_id}_{$i->batch_id}")
            ->toArray();

        $rowNum         = 0;
        $imported       = 0;
        $updated        = 0;
        $skipped        = 0;
        $linked         = 0;
        $batchesCreated = 0;

        DB::disableQueryLog();

        // -------------------------------------------------------------
        // 🚀 OPTIMIZATION 2: Disable Spatie activity logging temporarily
        // -------------------------------------------------------------
        if (function_exists('activity')) {
            activity()->disableLogging();
        }

        $this->command?->info("⚡ Starting high-speed processing...");

        try {
            DB::beginTransaction();

            while (($rawLine = fgets($file)) !== false) {
                $rowNum++;

                if ($rowNum <= 3) {
                    continue;
                }

                $cleanLine = trim($rawLine);
                if ($cleanLine === '') {
                    continue;
                }

                $sanitizedLine = preg_replace('/(?<!^)(?<!,)"(?!,)(?!$)/', '', $cleanLine);
                $cols          = str_getcsv($sanitizedLine, ',');

                $tNumber = '';
                $shift   = 0;

                $col0 = trim($cols[0] ?? '');
                $col1 = trim($cols[1] ?? '');

                if (preg_match('/^[A-Z0-9]{5,8}$/i', $col1) && ! str_contains(strtoupper($col1), 'NUMBER')) {
                    $tNumber = strtoupper($col1);
                    $shift   = 1;
                } elseif (preg_match('/^[A-Z0-9]{5,8}$/i', $col0) && ! str_contains(strtoupper($col0), 'NUMBER')) {
                    $tNumber = strtoupper($col0);
                    $shift   = 0;
                }

                if (empty($tNumber) || str_contains($tNumber, 'TRICASTLE') || str_contains($tNumber, 'T-NUMBER') || str_contains($tNumber, 'UNDECIDED')) {
                    $skipped++;
                    continue;
                }

                $rawName = trim($cols[$shift + 1] ?? '');
                if (empty($rawName) || $rawName === $tNumber) {
                    $rawName = trim($cols[$shift + 4] ?? $cols[$shift + 3] ?? $cols[$shift + 2] ?? '');
                }

                if (empty($rawName) || $rawName === $tNumber || str_contains(strtoupper($rawName), 'UNDECIDED')) {
                    $skipped++;
                    continue;
                }

                [$lastName, $firstName, $middleName] = $this->splitName($rawName);

                $dob            = $this->parseDate($cols[$shift + 7] ?? null);
                $gender         = $this->mapGender($cols[$shift + 8] ?? null);
                $firstBatchStr  = trim($cols[$shift + 9] ?? '');
                $latestBatchStr = trim($cols[$shift + 10] ?? '');

                if ($firstBatchStr === '' && $latestBatchStr === '') {
                    for ($i = 8; $i <= 12; $i++) {
                        $candidate = trim($cols[$shift + $i] ?? '');
                        if (preg_match('/batch\s*#?\d+/i', $candidate) || preg_match('/^\d{1,3}$/', $candidate)) {
                            $latestBatchStr = $candidate;
                            break;
                        }
                    }
                }

                $companyName    = trim($cols[$shift + 12] ?? $cols[$shift + 11] ?? '');
                $passportNo     = trim($cols[$shift + 30] ?? '') ?: null;
                $passportExpiry = $this->parseDate($cols[$shift + 31] ?? null);

                $empYear        = trim($cols[$shift + 32] ?? '');
                $empMonth       = trim($cols[$shift + 33] ?? '');
                $flightHist     = trim($cols[$shift + 19] ?? '');

                $realDeployedAt = $this->parseRealDeploymentDate($empYear, $empMonth, $flightHist);

                $address     = trim($cols[$shift + 35] ?? '') ?: null;
                $height      = $this->toHeight($cols[$shift + 36] ?? null);
                $weight      = $this->toWeight($cols[$shift + 37] ?? null);
                $civilStatus = $this->mapCivilStatus($cols[$shift + 38] ?? null);
                $children    = $this->toChildrenCount($cols[$shift + 39] ?? null);
                $religion    = trim($cols[$shift + 40] ?? '') ?: null;
                $hand        = $this->mapDominantHand($cols[$shift + 41] ?? null);
                $salary      = $this->toSalary($cols[$shift + 44] ?? null);
                $examScore   = $this->toScore($cols[$shift + 46] ?? null);
                $englishPct  = $this->toScore($cols[$shift + 47] ?? null);

                $isStaffMember = $this->isStaff($latestBatchStr) || $this->isStaff($firstBatchStr);
                $status        = $isStaffMember ? ApplicantStatus::Verified : ApplicantStatus::FinalList;

                $payload = [
                    'applicant_code'          => $tNumber,
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
                    'deleted_at'              => null,
                ];

                // Check in Memory HashMap instead of sending a SELECT query
                if (isset($existingApplicants[$tNumber])) {
                    $applicantId = $existingApplicants[$tNumber];
                    Applicant::where('id', $applicantId)->update($payload);
                    $updated++;
                } else {
                    $applicant   = Applicant::create($payload);
                    $applicantId = $applicant->id;
                    $existingApplicants[$tNumber] = $applicantId;
                    $imported++;
                }

                // Company Check
                if ($companyName !== '' && ! isset($existingCompanies[$companyName]) && class_exists(Company::class) && Schema::hasTable('companies')) {
                    try {
                        $compCode = 'COMP-' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $companyName));
                        if (strlen($compCode) < 6) {
                            $compCode = 'COMP-' . sprintf('%04d', (int) preg_replace('/\D/', '', $companyName) ?: rand(1000, 9999));
                        }

                        $comp = Company::create([
                            'name'    => $companyName,
                            'code'    => $compCode,
                            'country' => 'Japan',
                        ]);
                        $existingCompanies[$companyName] = $comp->id;
                    } catch (\Throwable $e) {
                        // Suppress
                    }
                }

                // Batch Check
                $extractedLatest = $this->extractBatchNumber($latestBatchStr);
                $extractedFirst  = $this->extractBatchNumber($firstBatchStr);

                $batchesToProcess = array_values(array_unique(array_filter(
                    [$extractedLatest, $extractedFirst],
                    fn ($v) => $v !== null && $v !== ''
                )));

                if (! $isStaffMember && ! empty($batchesToProcess)) {
                    foreach ($batchesToProcess as $batchNum) {
                        $batchKey = (string) $batchNum;

                        if (isset($existingBatches[$batchKey])) {
                            $batchId = $existingBatches[$batchKey];
                            if ($realDeployedAt) {
                                Batch::where('id', $batchId)->whereNull('deployment_date')->update([
                                    'deployment_date' => Carbon::parse($realDeployedAt)->toDateString(),
                                ]);
                            }
                        } else {
                            $batch = Batch::create([
                                'batch_number'    => $batchKey,
                                'name'            => "Batch {$batchKey}",
                                'country'         => 'Japan',
                                'status'          => $this->resolveBatchStatus('ongoing'),
                                'is_active'       => false,
                                'deployment_date' => $realDeployedAt ? Carbon::parse($realDeployedAt)->toDateString() : null,
                            ]);
                            $batchId = $batch->id;
                            $existingBatches[$batchKey] = $batchId;
                            $batchesCreated++;
                        }

                        // Link Check
                        $linkKey = "{$applicantId}_{$batchId}";
                        if (! isset($existingLinks[$linkKey])) {
                            ApplicantBatch::create([
                                'applicant_id'       => $applicantId,
                                'batch_id'           => $batchId,
                                'status'             => 'deployed',
                                'assigned_at'        => $realDeployedAt ?? now(),
                                'deployed_at'        => $realDeployedAt ?? now(),
                                'deployment_country' => 'Japan',
                                'deployment_company' => $companyName ?: null,
                            ]);
                            $existingLinks[$linkKey] = true;
                            $linked++;
                        }
                    }
                }

                if ($rowNum % 500 === 0) {
                    $this->command?->info("...processed {$rowNum} rows (Applicants: {$imported}, Batches: {$batchesCreated})");
                }
            }

            DB::commit();
            fclose($file);

            if (function_exists('activity')) {
                activity()->enableLogging();
            }

            $this->command?->info("✅ Seeder completed successfully!");
            $this->command?->table(
                ['Total CSV Lines', 'Created', 'Updated', 'Skipped', 'Batches Created', 'Links Created'],
                [[$rowNum, $imported, $updated, $skipped, $batchesCreated, $linked]]
            );

        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }
            if (function_exists('activity')) {
                activity()->enableLogging();
            }

            Log::error('LegacyApplicantsSeeder failed', [
                'message' => $e->getMessage(),
                'trace'   => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }

    private function resolveBatchStatus(string $desired = 'ongoing'): mixed
    {
        if (class_exists(BatchStatus::class)) {
            return BatchStatus::tryFrom($desired)
                ?? BatchStatus::tryFrom(strtoupper($desired))
                ?? BatchStatus::cases()[0]
                ?? $desired;
        }

        return $desired;
    }

    private function extractBatchNumber(string $batchStr): ?string
    {
        $batchStr = trim($batchStr);
        if ($batchStr === '' || $this->isStaff($batchStr)) {
            return null;
        }

        $upper = strtoupper($batchStr);
        if (in_array($upper, ['UNDECIDED', 'PENDING', 'NONE', 'N/A', 'NO BATCH', 'CANCELLED', 'REJECTED'])) {
            return null;
        }

        if (preg_match('/(\d+)/', $batchStr, $m)) {
            return (string) (int) $m[1];
        }

        return null;
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