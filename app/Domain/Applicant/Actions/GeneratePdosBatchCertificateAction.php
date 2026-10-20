<?php

namespace App\Domain\Applicant\Actions;

use App\Models\Company;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\TemplateProcessor;
use ReflectionClass;

class GeneratePdosBatchCertificateAction
{
    public function execute(Collection $applicants, array $options = []): string
    {
        $templatePath = resource_path('templates/PDOS.docx');

        if (!file_exists($templatePath)) {
            throw new \RuntimeException("PDOS template not found at {$templatePath}");
        }

        $formattedDate = isset($options['pdos_date']) && !empty($options['pdos_date'])
            ? date('F j, Y', strtotime($options['pdos_date']))
            : now()->format('F j, Y');

        $baseCertNo = $options['cert_no'] ?? null;

        $tempFile = tempnam(sys_get_temp_dir(), 'PDOS_BATCH_') . '.docx';

        $templateProcessor = new TemplateProcessor($templatePath);

        // 🎯 SAFELY clean split XML tags inside {{...}} in document.xml ONLY
        $this->sanitizeMainDocumentXml($templateProcessor);

        // Set macro delimiters to {{ and }}
        $templateProcessor->setMacroChars('{{', '}}');

        $chunks = $applicants->chunk(2)->values();
        $totalPages = $chunks->count();

        $certCounter = 0;

        for ($i = 0; $i < $totalPages; $i++) {
            $pair = $chunks[$i]->values();
            $top = $pair->get(0);
            $bottom = $pair->get(1);

            // ─── TOP APPLICANT ───────────────────────────────────────────────
            if ($top) {
                $certCounter++;
                $topCompany = $this->getCompany($top);

                $templateProcessor->setValue('APPLICANT_NAME_TOP', $this->getApplicantName($top));
                $templateProcessor->setValue('COMPANY_CATEGORY_TOP', $this->getCategoryName($top, $topCompany));
                $templateProcessor->setValue('COUNTRY_TOP', $this->getCountry($topCompany));
                $templateProcessor->setValue('COMPANY_TOP', $this->getForeignEmployer($topCompany));
                $templateProcessor->setValue('COMPANY_EMPLOYER_TOP', $this->getForeignPrincipal($topCompany));
            } else {
                $templateProcessor->setValue('APPLICANT_NAME_TOP', '');
                $templateProcessor->setValue('COMPANY_CATEGORY_TOP', '');
                $templateProcessor->setValue('COUNTRY_TOP', '');
                $templateProcessor->setValue('COMPANY_TOP', '');
                $templateProcessor->setValue('COMPANY_EMPLOYER_TOP', '');
            }

            // ─── BOTTOM APPLICANT ────────────────────────────────────────────
            if ($bottom) {
                $certCounter++;
                $bottomCompany = $this->getCompany($bottom);

                $templateProcessor->setValue('APPLICANT_NAME_BOTTOM', $this->getApplicantName($bottom));
                $templateProcessor->setValue('COMPANY_CATEGORY_BOTTOM', $this->getCategoryName($bottom, $bottomCompany));
                $templateProcessor->setValue('COUNTRY_BOTTOM', $this->getCountry($bottomCompany));
                $templateProcessor->setValue('COMPANY_BOTTOM', $this->getForeignEmployer($bottomCompany));
                $templateProcessor->setValue('COMPANY_EMPLOYER_BOTTOM', $this->getForeignPrincipal($bottomCompany));
            } else {
                $templateProcessor->setValue('APPLICANT_NAME_BOTTOM', '');
                $templateProcessor->setValue('COMPANY_CATEGORY_BOTTOM', '');
                $templateProcessor->setValue('COUNTRY_BOTTOM', '');
                $templateProcessor->setValue('COMPANY_BOTTOM', '');
                $templateProcessor->setValue('COMPANY_EMPLOYER_BOTTOM', '');
            }

            // ─── COMMON ──────────────────────────────────────────────────────
            $templateProcessor->setValue('PDOS_DATE', $formattedDate);

            // Format Certificate Number
            $certNoFormatted = $this->formatCertNo($baseCertNo, $certCounter);
            $templateProcessor->setValue('CERT_NO', $certNoFormatted);
        }

        $templateProcessor->saveAs($tempFile);

        return $tempFile;
    }

    /**
     * Safely clean split XML tags inside {{...}} in tempDocumentMainPart ONLY
     */
    private function sanitizeMainDocumentXml(TemplateProcessor $templateProcessor): void
    {
        $reflection = new ReflectionClass($templateProcessor);
        if (!$reflection->hasProperty('tempDocumentMainPart')) {
            return;
        }

        $property = $reflection->getProperty('tempDocumentMainPart');
        $property->setAccessible(true);
        $xml = $property->getValue($templateProcessor);

        if (is_string($xml)) {
            // 1. Join split braces: { <w:t> { </w:t> -> {{
            $xml = preg_replace('/\{\s*<[^>]+>\s*\{/', '{{', $xml);
            $xml = preg_replace('/\}\s*<[^>]+>\s*\}/', '}}', $xml);

            // 2. Strip XML tags strictly inside {{...}} so PHPWord can match {{VAR}}
            $xml = preg_replace_callback('/\{\{(.*?)\}\}/s', function ($matches) {
                return '{{' . strip_tags($matches[1]) . '}}';
            }, $xml);

            $property->setValue($templateProcessor, $xml);
        }
    }

    private function getCompany($applicant): ?Company
    {
        if (!$applicant) return null;
        $batches = $applicant->applicantBatches ?? $applicant->applicant_batches ?? null;
        if ($batches && $batches->isNotEmpty()) {
            return $batches->first()->batch?->company ?? null;
        }
        return null;
    }

    private function getApplicantName($applicant): string
    {
        if (!$applicant) return '';
        $name = $applicant->full_name ?? trim("{$applicant->first_name} {$applicant->middle_name} {$applicant->last_name}");
        return mb_strtoupper($name ?: '');
    }

    private function getCategoryName($applicant, ?Company $company): string
    {
        if ($company && $company->category && !empty($company->category->name)) {
            return mb_strtoupper($company->category->name);
        }

        if ($applicant) {
            $skill = $applicant->applied_position 
                ?? $applicant->skill_category 
                ?? $applicant->trade_or_occupation 
                ?? 'WORKER';
            return mb_strtoupper($skill);
        }

        return 'WORKER';
    }

    private function getCountry(?Company $company): string
    {
        if ($company && !empty($company->country)) {
            return mb_strtoupper($company->country);
        }
        return 'JAPAN';
    }

    private function getForeignEmployer(?Company $company): string
    {
        if ($company) {
            $name = $company->name_on_document ?? $company->name ?? null;
            if ($name) {
                return mb_strtoupper($name);
            }
        }
        return 'N/A';
    }

    private function getForeignPrincipal(?Company $company): string
    {
        if ($company) {
            $principal = $company->signatory_name 
                ?? $company->contact_person 
                ?? $company->name 
                ?? null;
            if ($principal) {
                return mb_strtoupper($principal);
            }
        }
        return 'N/A';
    }

    private function formatCertNo(?string $baseCertNo, int $index): string
    {
        if (empty($baseCertNo)) {
            return '';
        }

        if (preg_match('/^(.*?)(\d+)$/', $baseCertNo, $matches)) {
            $prefix = $matches[1];
            $number = (int) $matches[2];
            $length = strlen($matches[2]);

            $nextNumber = $number + ($index - 1);
            return $prefix . str_pad((string) $nextNumber, $length, '0', STR_PAD_LEFT);
        }

        return $baseCertNo;
    }
}