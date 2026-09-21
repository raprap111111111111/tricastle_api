<?php

namespace App\Domain\Applicant\Services;

use App\Models\Applicant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use Throwable;
use ZipArchive;

class InsuranceFormGeneratorService
{
    private function disk(): string
    {
        $default = config('filesystems.default', 'public');
        return $default === 'local' ? 'public' : $default;
    }

    private function clean(mixed $value): string
    {
        if ($value === null) {
            return '';
        }
        $str = (string) $value;
        if (! mb_check_encoding($str, 'UTF-8')) {
            $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
        }
        return trim(str_replace(["\xe2\x80\x93", "\xe2\x80\x94", '–', '—'], '-', $str));
    }

    private function resolveTemplatePath(): string
    {
        $docx = resource_path('templates/MIGRANT_INSURANCE_PAX.docx');

        if (file_exists($docx) && filesize($docx) > 0) {
            return $docx;
        }

        throw new RuntimeException("Migrant Insurance template not found at: {$docx}");
    }

    private function repairXmlRunSplitting(string $docxFilePath): void
    {
        $zip = new ZipArchive();
        if ($zip->open($docxFilePath) !== true) {
            return;
        }

        for ($i = 0; $i < $zip->numFiles; $i++) {
            $filename = $zip->getNameIndex($i);
            if (! str_starts_with((string) $filename, 'word/') || ! str_ends_with((string) $filename, '.xml')) {
                continue;
            }

            $xml = $zip->getFromIndex($i);
            if ($xml === false) {
                continue;
            }

            $repairedXml = preg_replace_callback('/\{\{(?:[^}]|<[^>]+>)*\}\}/s', function ($matches) {
                return strip_tags($matches[0]);
            }, $xml);

            if (is_string($repairedXml)) {
                $repairedXml = str_replace(['{{{{', '}}}}'], ['{{', '}}'], $repairedXml);
            }

            if (is_string($repairedXml) && $repairedXml !== $xml) {
                $zip->addFromString($filename, $repairedXml);
            }
        }

        $zip->close();
    }

    private function generateDownloadUrl(string $fileName): string
    {
        $diskName = $this->disk();
        $disk     = Storage::disk($diskName);

        try {
            return $disk->temporaryUrl($fileName, now()->addMinutes(60));
        } catch (Throwable $e) {
            // Local fallback
        }

        return url(Storage::url($fileName));
    }

    public function generate(Collection $applicants, string $departureDate, array $frontendData = []): array
    {
        $templatePath = $this->resolveTemplatePath();

        $workFile = sys_get_temp_dir() . '/insurance_work_' . uniqid('', true) . '.docx';
        $outFile  = sys_get_temp_dir() . '/insurance_out_' . uniqid('', true) . '.docx';

        if (! @copy($templatePath, $workFile)) {
            throw new RuntimeException("Failed to copy template to working file: {$workFile}");
        }

        $this->repairXmlRunSplitting($workFile);

        try {
            Settings::setOutputEscapingEnabled(true);

            $templateProcessor = new TemplateProcessor($workFile);
            $templateProcessor->setMacroChars('{{', '}}');

            // 🎯 1. Multi-Case PAX Placeholders (Matches {{Pax}}, {{PAX}}, {{pax}}, {{PAX_COUNT}})
            $paxCount = $applicants->count();

            $templateProcessor->setValues([
                'Pax'            => $this->clean($paxCount),
                'PAX'            => $this->clean($paxCount),
                'pax'            => $this->clean($paxCount),
                'PAX_COUNT'      => $this->clean($paxCount),
                'pax_count'      => $this->clean($paxCount),
                'PAX_TEXT'       => $this->clean("{$paxCount}PAX"),
                'PAX_TEXT_SPACE' => $this->clean("{$paxCount} PAX"),
                'DEPARTURE_DATE' => $this->clean(strtoupper($departureDate)),
            ]);

            // 2. Tabular Data
            $tableData = [];
            $frontendCollection = collect($frontendData);

            foreach ($applicants->values() as $index => $applicant) {
                $override = $frontendCollection->firstWhere('id', $applicant->id) ?? [];

                $internships       = $applicant->internships;
                $currentInternship = $internships?->firstWhere('is_current', true) ?? $internships?->first();

                $firstBatch          = $applicant->batches?->first();
                $firstApplicantBatch = $applicant->applicantBatches?->first();

                $dbCompany = $currentInternship?->receivingCompany?->name
                    ?? $currentInternship?->acceptingCompany?->name
                    ?? $firstBatch?->receivingCompany?->name
                    ?? $firstBatch?->acceptingCompany?->name
                    ?? $firstBatch?->company?->name
                    ?? $firstApplicantBatch?->deployment_company
                    ?? $applicant->preferred_work_location
                    ?? 'N/A';

                $dob = ! empty($override['date_of_birth'] ?? $override['dob'] ?? null)
                    ? $override['date_of_birth'] ?? $override['dob']
                    : ($applicant->date_of_birth?->format('Y-m-d') ?? 'N/A');

                $passport = ! empty($override['passport_number'] ?? $override['passport'] ?? null)
                    ? $override['passport_number'] ?? $override['passport']
                    : ($applicant->passport_number ?? 'N/A');

                $trade = ! empty($override['occupation'] ?? $override['trade'] ?? null)
                    ? $override['occupation'] ?? $override['trade']
                    : ($applicant->trade_or_occupation ?? 'N/A');

                $company = ! empty($override['foreign_employer'] ?? $override['company'] ?? null)
                    ? $override['foreign_employer'] ?? $override['company']
                    : $dbCompany;

                $country = ! empty($override['country_destination'] ?? $override['country'] ?? null)
                    ? $override['country_destination'] ?? $override['country']
                    : 'JAPAN';

                $durationRaw = $override['contract_duration_months'] ?? $override['duration'] ?? '36';
                $durationStr = is_numeric($durationRaw)
                    ? "{$durationRaw} MONTHS"
                    : (str_contains(strtoupper((string) $durationRaw), 'MONTH')
                        ? strtoupper((string) $durationRaw)
                        : "{$durationRaw} MONTHS");

                $tableData[] = [
                    'INDEX'    => $this->clean($index + 1),
                    'NAME'     => $this->clean(strtoupper($applicant->full_name)),
                    'DOB'      => $this->clean($dob),
                    'PASSPORT' => $this->clean(strtoupper($passport)),
                    'TRADE'    => $this->clean(strtoupper($trade)),
                    'COMPANY'  => $this->clean(strtoupper($company)),
                    'COUNTRY'  => $this->clean(strtoupper($country)),
                    'DURATION' => $this->clean(strtoupper($durationStr)),
                ];
            }

            // 3. Clone template rows dynamically
            $templateProcessor->cloneRowAndSetValues('INDEX', $tableData);
            $templateProcessor->saveAs($outFile);

            // 4. Save Export
            $fileName = sprintf(
                'exports/insurance/MIGRANT_INSURANCE_%sPAX_%s.docx',
                $paxCount,
                now()->format('Ymd_His')
            );

            Storage::disk($this->disk())->put($fileName, file_get_contents($outFile));

        } catch (Throwable $e) {
            Log::error('[INSURANCE] Word processor generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new RuntimeException('Migrant Insurance DOCX generation failed: ' . $e->getMessage(), 0, $e);
        } finally {
            @unlink($workFile);
            @unlink($outFile);
        }

        return [
            'file_name'    => basename($fileName),
            'pax_count'    => $paxCount,
            'download_url' => $this->generateDownloadUrl($fileName),
        ];
    }
}