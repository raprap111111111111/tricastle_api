<?php

namespace App\Domain\Internship\Services;

use App\Domain\Internship\DTOs\GenerateMoaDTO;
use App\Models\ApplicantInternship;
use App\Models\InternshipDocument;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\Settings;
use PhpOffice\PhpWord\TemplateProcessor;
use RuntimeException;
use Throwable;
use ZipArchive;

class MoaGeneratorService
{
    public function __construct(
        private readonly InternshipResolverService $resolver,
    ) {}

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
        $docx = resource_path('templates/MOA_MASTER_TEMPLATE.docx');
        $doc  = resource_path('templates/MOA_MASTER_TEMPLATE.doc');

        if (file_exists($docx) && filesize($docx) > 0) {
            return $docx;
        }

        if (file_exists($doc) && filesize($doc) > 0) {
            throw new RuntimeException(
                "Found legacy .doc template only. PHPWord needs .docx.\n" .
                    "Open the .doc in WPS/Word → Save As → Word Document (*.docx)\n" .
                    "Save to: {$docx}"
            );
        }

        throw new RuntimeException("MOA master template not found at: {$docx}");
    }

    private function assertValidDocx(string $path): void
    {
        $fh = fopen($path, 'rb');
        $header = $fh ? fread($fh, 4) : false;
        if ($fh) {
            fclose($fh);
        }

        if ($header === false || bin2hex($header) !== '504b0304') {
            throw new RuntimeException(
                "Template is not a valid .docx (ZIP). Header=" . bin2hex((string) $header) .
                    " Path={$path}. Re-save as Word Document (*.docx)."
            );
        }

        $zip = new ZipArchive();
        $ok = $zip->open($path, ZipArchive::CHECKCONS);
        if ($ok !== true) {
            throw new RuntimeException("Template ZIP open failed. code={$ok} path={$path}");
        }
        if ($zip->locateName('word/document.xml') === false) {
            $zip->close();
            throw new RuntimeException("Template missing word/document.xml: {$path}");
        }
        $zip->close();
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

            // 🎯 FIXED: return strip_tags($matches[0]) instead of '{{'.strip_tags(...).'}}'
            $repairedXml = preg_replace_callback('/\{\{(?:[^}]|<[^>]+>)*\}\}/s', function ($matches) {
                return strip_tags($matches[0]);
            }, $xml);

            // Clean up any pre-existing quadrupled braces ({{{{TAG}}}}) if present in XML
            if (is_string($repairedXml)) {
                $repairedXml = str_replace(['{{{{', '}}}}'], ['{{', '}}'], $repairedXml);
            }

            if (is_string($repairedXml) && $repairedXml !== $xml) {
                $zip->addFromString($filename, $repairedXml);
            }
        }

        $zip->close();
    }

    private function postProcessRemoveDoubleBraces(string $docxFilePath): void
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

            // Remove leftover {{ and }} wrapping around replaced values
            $cleanedXml = preg_replace_callback('/\{\{([^}]+)\}\}/u', function ($matches) {
                $content = $matches[1];
                // Keep unreplaced macro keys (e.g. UPPERCASE_WITH_UNDERSCORES) intact
                if (preg_match('/^[A-Z0-9_]+$/', trim($content))) {
                    return $matches[0];
                }
                return $content;
            }, $xml);

            if (is_string($cleanedXml) && $cleanedXml !== $xml) {
                $zip->addFromString($filename, $cleanedXml);
            }
        }

        $zip->close();
    }

    public function generate(ApplicantInternship $internship, GenerateMoaDTO $dto): InternshipDocument
    {
        $payload = $this->resolver->resolve($internship, $dto);

        $templatePath = $this->resolveTemplatePath();
        $this->assertValidDocx($templatePath);

        $workFile = sys_get_temp_dir() . '/moa_work_' . uniqid('', true) . '.docx';
        $outFile  = sys_get_temp_dir() . '/moa_out_' . uniqid('', true) . '.docx';

        if (! @copy($templatePath, $workFile)) {
            throw new RuntimeException("Failed to copy template to working file: {$workFile}");
        }

        $this->repairXmlRunSplitting($workFile);
        $this->assertValidDocx($workFile);

        try {
            Settings::setOutputEscapingEnabled(true);

            $templateProcessor = new TemplateProcessor($workFile);
            $templateProcessor->setMacroChars('{{', '}}');

            $stipend = (float) ($payload['compensation']['stipend'] ?? 0);
            $meal    = (float) ($payload['compensation']['meal_allowance'] ?? 0);
            $total   = (float) ($payload['compensation']['total'] ?? ($stipend + $meal));

            $templateProcessor->setValues([
                'DAY' => $this->clean($payload['agreement_day_ordinal'] ?? '____'),
                'MONTH' => $this->clean($payload['agreement_month'] ?? '__________'),
                'YEAR' => $this->clean($payload['agreement_year'] ?? '____'),
                'CITY' => $this->clean($payload['municipality'] ?? 'Murcia'),

                'I_NAME' => $this->clean($payload['intern']['full_name'] ?? '________________________'),
                'I_AGE' => $this->clean($payload['intern']['age'] ?: '___'),
                'I_STAT' => $this->clean($payload['intern']['civil_status'] ?? 'Single'),
                'I_ADDR' => $this->clean($payload['intern']['address'] ?? '________________________________________________'),
                'I_PASSPORT' => $this->clean($payload['intern']['passport'] ?? '___________'),
                'INTERN_PASS_ISSUED' => $this->clean($payload['intern']['passport_expiry'] ?? '__________________'),

                'G1_NAME' => $this->clean($payload['guarantor_1']['full_name'] ?: '________________________'),
                'G1_AGE' => $this->clean($payload['guarantor_1']['age'] ?: '___'),
                'G1_STAT' => $this->clean($payload['guarantor_1']['civil_status'] ?: 'Single'),
                'G1_ADDR' => $this->clean($payload['guarantor_1']['address'] ?: '________________________________________________'),
                'G1_CER' => $this->clean($payload['guarantor_1']['residence_cert_no'] ?: '___________'),
                'G1_CERT_ISSUED' => $this->clean($payload['guarantor_1']['residence_cert_issued'] ?: '__________________'),

                'G2_NAME' => $this->clean($payload['guarantor_2']['full_name'] ?: '________________________'),
                'G2_AGE' => $this->clean($payload['guarantor_2']['age'] ?: '___'),
                'G2_STAT' => $this->clean($payload['guarantor_2']['civil_status'] ?: 'Single'),
                'G2_ADDR' => $this->clean($payload['guarantor_2']['address'] ?: '________________________________________________'),
                'G2_CER' => $this->clean($payload['guarantor_2']['residence_cert_no'] ?: '___________'),
                'G2_CERT_ISSUED' => $this->clean($payload['guarantor_2']['residence_cert_issued'] ?: '__________________'),

                'RECEIVING_COMPANY' => $this->clean($payload['receiving_company'] ?? '________________________'),
                'CON_YEAR' => $this->clean($payload['contract_start_y'] ?? '____'),
                'CON_MONTH' => $this->clean($payload['contract_start_m'] ?? '__'),
                'CON_DAY' => $this->clean($payload['contract_start_d'] ?? '__'),
                'CE_YEAR' => $this->clean($payload['contract_end_y'] ?? '____'),
                'CE_MONTH' => $this->clean($payload['contract_end_m'] ?? '__'),
                'CE_MON' => $this->clean($payload['contract_end_m'] ?? '__'),
                'CE_DAY' => $this->clean($payload['contract_end_d'] ?? '__'),

                'PLACE_OF_INTERNSHIP' => $this->clean($payload['place_of_internship'] ?? '________________________________'),
                'JOB_DESCRIPTION' => $this->clean($payload['job_description'] ?? '________________'),
                'SCHEDULE_DAYS' => $this->clean($payload['schedule']['days'] ?? 'MONDAY to SATURDAY'),
                'DAYS_OFF' => $this->clean($payload['schedule']['day_off'] ?? 'SUNDAY'),
                'SCHED_START' => $this->clean($payload['schedule']['start'] ?? '8:00 AM'),
                'SCHED_S' => $this->clean($payload['schedule']['start'] ?? '8:00 AM'),
                'SCHED_END' => $this->clean($payload['schedule']['end'] ?? '6:00 PM'),
                'LUNCH_BREAK' => $this->clean($payload['schedule']['lunch_break'] ?? '40-50 mins.'),

                'TOTAL_PAY' => number_format($total),
                'STIPEND' => number_format($stipend),
                'MEAL' => number_format($meal),
            ]);

            // Save to working output file
            $templateProcessor->saveAs($outFile);

            // 🎯 Post-process to guarantee removal of any leftover {{ and }} around generated text
            $this->postProcessRemoveDoubleBraces($outFile);

            $code = preg_replace('/[^A-Za-z0-9_-]/', '', (string) ($payload['intern']['code'] ?? ''));
            $name = preg_replace('/[^A-Za-z0-9_-]/', '_', preg_replace('/\s+/', '_', (string) ($payload['intern']['full_name'] ?? '')));
            $name = trim(preg_replace('/_+/', '_', (string) $name), '_');

            $prefix = implode('_', array_filter([$code, $name])) ?: ('INT_' . $internship->id);

            $fileName = sprintf(
                'internships/%s/%s_MOA_%s.docx',
                now()->format('Y/m'),
                strtoupper($prefix),
                now()->format('Ymd_His')
            );

            Storage::disk($this->disk())->put($fileName, file_get_contents($outFile));
        } catch (Throwable $e) {
            Log::error('[MOA] Word processor generation failed', [
                'internship_id' => $internship->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            throw new RuntimeException('MOA DOCX generation failed: ' . $e->getMessage(), 0, $e);
        } finally {
            @unlink($workFile);
            @unlink($outFile);
        }

        return InternshipDocument::create([
            'applicant_internship_id' => $internship->id,
            'document_type' => 'moa',
            'document_no' => $this->nextDocumentNo(),
            'file_path' => $fileName,
            'status' => 'generated',
            'generated_by' => $dto->generatedBy,
            'snapshot' => $payload,
        ]);
    }

    private function urlFor(InternshipDocument $doc): string
    {
        return Storage::disk($this->disk())->temporaryUrl(
            $doc->file_path,
            now()->addMinutes(60)   // link expires after 1 hour
        );
    }

    public function generateBulk(Collection $internships, GenerateMoaDTO $dto): array
    {
        $docs = collect();
        foreach ($internships as $internship) {
            $docs->push($this->generate($internship, $dto));
        }

        if ($docs->count() === 1) {
            return [
                'type'      => 'single',
                'documents' => $docs,
                'download'  => $this->urlFor($docs->first()),
            ];
        }

        $zipPath = $this->zip($docs);

        return [
            'type'      => 'zip',
            'documents' => $docs,
            'download'  => Storage::disk($this->disk())->temporaryUrl(
                $zipPath,
                now()->addMinutes(60)
            ),
        ];
    }

    private function nextDocumentNo(): string
    {
        $year = now()->format('Y');
        $count = InternshipDocument::where('document_type', 'moa')
            ->whereYear('created_at', $year)
            ->count() + 1;

        return sprintf('MOA-%s-%05d', $year, $count);
    }

    private function zip(Collection $docs): string
    {
        $disk = $this->disk();
        $zipName = 'internships/bulk/MOA_BULK_' . now()->format('Ymd_His') . '.zip';
        Storage::disk($disk)->makeDirectory('internships/bulk');

        $tmpZip = sys_get_temp_dir() . '/moa_bulk_' . uniqid('', true) . '.zip';
        $zip = new ZipArchive();
        $zip->open($tmpZip, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        foreach ($docs as $doc) {
            $contents = Storage::disk($disk)->get($doc->file_path);
            if ($contents !== null) {
                $zip->addFromString(basename($doc->file_path), $contents);
            }
        }
        $zip->close();

        Storage::disk($disk)->put($zipName, file_get_contents($tmpZip));
        @unlink($tmpZip);

        return $zipName;
    }
}
