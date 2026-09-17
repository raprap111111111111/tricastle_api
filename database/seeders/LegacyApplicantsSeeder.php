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
        $candidates = [
            database_path('seeders/data/legacy_applicants.csv'),
            public_path('legacy_applicants(final).csv'),
            public_path('legacy_applicants.csv'),
        ];

        $filePath = null;
        foreach ($candidates as $path) {
            if (file_exists($path) && filesize($path) > 0) {
                $filePath = $path;
                break;
            }
        }

        if (! $filePath) {
            $this->command?->error('❌ CSV file not found.');
            return;
        }

        $this->command?->info("📄 Found CSV: {$filePath}");
        $this->command?->info('📦 Copying to isolated temp storage to prevent Docker I/O errors...');

        // Copy file to /tmp to prevent Docker macOS errno=5 volume disconnects
        $tmpPath = sys_get_temp_dir() . '/legacy_applicants_' . uniqid('', true) . '.csv';
        
        if (! @copy($filePath, $tmpPath)) {
            // Fallback stream copy if simple copy fails
            $in  = fopen($filePath, 'rb');
            $out = fopen($tmpPath, 'wb');
            if (! $in || ! $out) {
                $this->command?->error('❌ Unable to copy CSV to temp.');
                return;
            }
            stream_copy_to_stream($in, $out);
            fclose($in);
            fclose($out);
        }

        $file = fopen($tmpPath, 'r');
        if (! $file) {
            $this->command?->error('❌ Unable to open temp CSV file.');
            return;
        }

        $this->command?->info('🚀 Loading pre-fetched database lookups into memory...');

        $existingApplicants = Applicant::withTrashed()->pluck('id', 'applicant_code')->toArray();
        $existingBatches    = Batch::withTrashed()->pluck('id', 'batch_number')->toArray();
        $existingCompanies  = class_exists(Company::class) && Schema::hasTable('companies')
            ? Company::pluck('id', 'name')->toArray()
            : [];

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
        $passportsFound = 0;

        $headerMap = [];

        DB::disableQueryLog();

        if (function_exists('activity')) {
            activity()->disableLogging();
        }

        $this->command?->info('⚡ Starting high-speed processing...');

        try {
            DB::beginTransaction();

            // Use fgetcsv to properly handle multi-line cells and quotes
            while (($cols = fgetcsv($file)) !== false) {
                $rowNum++;

                // Skip completely empty lines
                if ($cols === [null] || empty(array_filter($cols, fn($v) => trim((string)$v) !== ''))) {
                    continue;
                }

                // Clean all column strings
                $cols = array_map(fn ($v) => trim((string) $v), $cols);

                // Row 2 = actual headers
                if ($rowNum === 2) {
                    foreach ($cols as $idx => $headerName) {
                        $cleanHeader = strtoupper(trim(preg_replace('/\s+/', ' ', (string) $headerName)));
                        $cleanHeader = str_replace(["\n", "\r"], ' ', $cleanHeader);
                        if ($cleanHeader !== '') {
                            $headerMap[$cleanHeader] = $idx;
                        }
                    }
                    continue;
                }

                // Skip instruction/formula rows
                if ($rowNum <= 3) {
                    continue;
                }

                $get = function (array $keywords) use ($cols, $headerMap) {
                    foreach ($keywords as $keyword) {
                        $keyword = strtoupper(trim($keyword));
                        foreach ($headerMap as $header => $idx) {
                            if (str_contains($header, $keyword) && array_key_exists($idx, $cols)) {
                                $val = trim((string) $cols[$idx]);
                                if ($val !== '') {
                                    return $val;
                                }
                            }
                        }
                    }
                    return null;
                };

                // T-Number
                $tNumber = strtoupper(trim((string) ($get(['T-NUMBER', 'TRICASTLE']) ?? '')));
                if ($tNumber === '' || ! preg_match('/^[TMG]\d{5}$/i', $tNumber)) {
                    foreach ($cols as $cVal) {
                        $v = strtoupper(trim((string) $cVal));
                        if (preg_match('/^[TMG]\d{5}$/i', $v)) {
                            $tNumber = $v;
                            break;
                        }
                    }
                }

                if (
                    $tNumber === '' ||
                    str_contains($tNumber, 'TRICASTLE') ||
                    str_contains($tNumber, 'T-NUMBER') ||
                    str_contains($tNumber, 'UNDECIDED')
                ) {
                    $skipped++;
                    continue;
                }

                // Name
                $rawName = trim((string) ($get(['NAME']) ?? ''));
                if ($rawName === '' || $rawName === $tNumber) {
                    foreach ($cols as $cVal) {
                        $v = trim((string) $cVal);
                        if ($v === '' || strtoupper($v) === $tNumber || is_numeric($v)) {
                            continue;
                        }
                        $up = strtoupper($v);
                        if (str_contains($up, 'UNDECIDED') || str_contains($up, 'STAFF') || str_contains($up, 'FUNCTION')) {
                            continue;
                        }
                        if (preg_match('/^[A-Za-z0-9\s,.\'\-]+$/u', $v) && strlen($v) >= 3) {
                            $rawName = $v;
                            break;
                        }
                    }
                }

                if ($rawName === '' || str_contains(strtoupper($rawName), 'UNDECIDED')) {
                    $skipped++;
                    continue;
                }

                [$lastName, $firstName, $middleName] = $this->splitName($rawName);

                $dob            = $this->parseDate($get(['DATE OF BIRTH', 'BIRTHDAY', 'BIRTH']));
                $gender         = $this->mapGender($get(['GENDER', 'SEX']));
                $firstBatchStr  = (string) ($get(['FIRST BATCH']) ?? '');
                $latestBatchStr = (string) ($get(['LATEST BATCH']) ?? '');
                $companyName    = (string) ($get(['LATEST COMPANY', 'COMPANY NUMBER', 'COMPANY']) ?? '');

                // ✅ PASSPORT NUMBER
                $passportNo = $this->cleanPassport($get(['PASSPORT NUMBER', 'PASSPORT NO', 'PASSPORT']));

                // ✅ PASSPORT EXPIRY
                $passportExpiry = $this->parseDate($get(['DATE OF EXPIRATION', 'PASSPORT EXPIRY', 'EXPIRATION', 'EXPIRY']));

                // Fallback: find passport anywhere, then take nearby expiry date
                if (! $passportNo || ! $passportExpiry) {
                    foreach ($cols as $idx => $cVal) {
                        $candidate = $this->cleanPassport($cVal);
                        if (! $candidate) {
                            continue;
                        }

                        if (! $passportNo) {
                            $passportNo = $candidate;
                        }

                        if (! $passportExpiry) {
                            for ($j = 1; $j <= 2; $j++) {
                                if (! isset($cols[$idx + $j])) {
                                    continue;
                                }
                                $maybeExpiry = $this->parseDate($cols[$idx + $j]);
                                if ($maybeExpiry) {
                                    $passportExpiry = $maybeExpiry;
                                    break;
                                }
                            }
                        }
                        break;
                    }
                }

                if ($passportNo) {
                    $passportsFound++;
                }

                $empYear    = (string) ($get(['EMPLOYMENT YEAR']) ?? '');
                $empMonth   = (string) ($get(['EMPLOYMENT MONTH']) ?? '');
                $flightHist = (string) ($get(['FLIGHT']) ?? '');
                $realDeployedAt = $this->parseRealDeploymentDate($empYear, $empMonth, $flightHist);

                $address     = $get(['CURRENT ADDRESS', 'HOME TOWN', 'PLACE OF BIRTH']);
                $height      = $this->toHeight($get(['HEIGHT']));
                $weight      = $this->toWeight($get(['WEIGHT']));
                $civilStatus = $this->mapCivilStatus($get(['CIVIL STATUS', 'MARITAL STATUS']));
                $children    = $this->toChildrenCount($get(['NUMBER OF CHILDREN', 'CHILDREN']));
                $religion    = $get(['RELIGION']);
                $hand        = $this->mapDominantHand($get(['DOMINANT HAND', 'HAND']));
                $salary      = $this->toSalary($get(['CURRENT SALARY', 'SALARY']));
                $examScore   = $this->toScore($get(['EXAM RESULT', 'EXAM']));
                $englishPct  = $this->toScore($get(['ENGLISH']));

                if ($religion && is_numeric($religion)) {
                    $religion = null;
                }

                $isStaffMember = $this->isStaff($latestBatchStr) || $this->isStaff($firstBatchStr) || $this->isStaff(implode(' ', $cols));
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

                if (Schema::hasColumn('applicants', 'passport_issue_date')) {
                    $payload['passport_issue_date'] = null;
                }
                if (Schema::hasColumn('applicants', 'passport_issue_place')) {
                    $payload['passport_issue_place'] = null;
                }

                if (isset($existingApplicants[$tNumber])) {
                    $applicantId = $existingApplicants[$tNumber];
                    Applicant::where('id', $applicantId)->update($payload);
                    $updated++;
                } else {
                    $applicant = Applicant::create($payload);
                    $applicantId = $applicant->id;
                    $existingApplicants[$tNumber] = $applicantId;
                    $imported++;
                }

                // Company
                if ($companyName !== '' && ! isset($existingCompanies[$companyName]) && class_exists(Company::class) && Schema::hasTable('companies')) {
                    try {
                        $compCode = 'COMP-' . strtoupper(preg_replace('/[^A-Za-z0-9]/', '', $companyName));
                        if (strlen($compCode) < 6) {
                            $compCode = 'COMP-' . sprintf('%04d', random_int(1000, 9999));
                        }

                        $comp = Company::create([
                            'name'    => $companyName,
                            'code'    => $compCode,
                            'country' => 'Japan',
                        ]);
                        $existingCompanies[$companyName] = $comp->id;
                    } catch (\Throwable $e) {
                        // ignore
                    }
                }

                // Batches
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
                    $this->command?->info("...processed {$rowNum} rows (Imported: {$imported}, Updated: {$updated}, Passports: {$passportsFound})");
                }
            }

            DB::commit();
            fclose($file);
            @unlink($tmpPath);

            if (function_exists('activity')) {
                activity()->enableLogging();
            }

            $this->command?->info('✅ Seeder completed successfully!');
            $this->command?->table(
                ['Total CSV Lines', 'Created', 'Updated', 'Skipped', 'Passports Found', 'Batches Created', 'Links Created'],
                [[$rowNum, $imported, $updated, $skipped, $passportsFound, $batchesCreated, $linked]]
            );
        } catch (\Throwable $e) {
            DB::rollBack();
            if (isset($file) && is_resource($file)) {
                fclose($file);
            }
            if (isset($tmpPath)) {
                @unlink($tmpPath);
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

    private function cleanPassport(mixed $value): ?string
    {
        $value = strtoupper(trim((string) $value));
        if ($value === '') {
            return null;
        }

        if (in_array($value, ['SINGLE', 'MARRIED', 'WIDOWED', 'DIVORCED', 'SEPARATED', 'STAFF', 'NONE', 'N/A', 'MALE', 'FEMALE'], true)) {
            return null;
        }

        if (preg_match('/^(?:[A-Z]{1,2}\d{6,8}[A-Z0-9]?|[A-Z]\d{3}[A-Z]\d{4})$/', $value)) {
            return $value;
        }

        return null;
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
        if (in_array($upper, ['UNDECIDED', 'PENDING', 'NONE', 'N/A', 'NO BATCH', 'CANCELLED', 'REJECTED'], true)) {
            return null;
        }

        if (preg_match('/(\d+)/', $batchStr, $m)) {
            return (string) ((int) $m[1]);
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
            $last = trim($last);
            $parts = preg_split('/\s+/', trim($rest)) ?: [];
            $first = array_shift($parts) ?? '';
            $middle = implode(' ', $parts);

            return [$last, $first, $middle];
        }

        $parts = preg_split('/\s+/', $fullName) ?: [];
        $last = array_shift($parts) ?? '';
        $first = array_shift($parts) ?? '';
        $middle = implode(' ', $parts);

        return [$last, $first, $middle];
    }

    private function parseDate(mixed $value): ?string
    {
        $value = trim((string) $value);
        if ($value === '' || str_contains($value, '#')) {
            return null;
        }

        if (is_numeric($value) && (int) $value > 10000 && (int) $value < 60000) {
            try {
                return Carbon::create(1899, 12, 30)->addDays((int) $value)->format('Y-m-d');
            } catch (\Throwable) {
                return null;
            }
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
            'M', 'MALE', 'Ｍ' => 'male',
            'F', 'FEMALE', 'Ｆ' => 'female',
            default => null,
        };
    }

    private function mapCivilStatus(mixed $value): string
    {
        $v = strtolower(trim((string) $value));

        return match (true) {
            str_contains($v, 'married') => 'married',
            str_contains($v, 'widow') => 'widowed',
            str_contains($v, 'separat') => 'separated',
            str_contains($v, 'divor') => 'divorced',
            default => 'single',
        };
    }

    private function mapDominantHand(mixed $value): ?string
    {
        $v = strtolower(trim((string) $value));

        return match (true) {
            str_contains($v, 'left') => 'left',
            str_contains($v, 'right') => 'right',
            str_contains($v, 'both'), str_contains($v, 'ambi') => 'both',
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