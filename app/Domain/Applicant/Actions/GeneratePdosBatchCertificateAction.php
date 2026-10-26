<?php

namespace App\Domain\Applicant\Actions;

use App\Models\Company;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\TemplateProcessor;
use ZipArchive;

class GeneratePdosBatchCertificateAction
{
    private const FOREIGN_PRINCIPAL = 'MULTI CONTRACTORS COOPERATIVE';

    public function execute(Collection $applicants, array $options = []): string
    {
        $templatePath = resource_path('templates/PDOS.docx');
        if (! file_exists($templatePath)) {
            $alt = storage_path('app/templates/PDOS.docx');
            if (! file_exists($alt)) {
                throw new \RuntimeException("PDOS template not found at {$templatePath}");
            }
            $templatePath = $alt;
        }

        $formattedDate = ! empty($options['pdos_date'])
            ? date('F j, Y', strtotime($options['pdos_date']))
            : now()->format('F j, Y');

        $baseCertNo = $options['cert_no'] ?? null;

        // Repair template XML to clean split tags and stray brackets like {{COMPANY_CATEGORY_TOP}}]
        $cleanTemplatePath = $this->prepareCleanTemplate($templatePath);

        // Chunk applicants: exactly 2 applicants per physical page
        $chunks = $applicants->values()->chunk(2)->values();
        $pageFiles = [];
        $certCounter = 0;

        foreach ($chunks as $pair) {
            $pair = $pair->values();
            $top = $pair->get(0);
            $bottom = $pair->get(1); // null when count is odd (e.g. 3rd applicant alone on page 2)

            $pageFile = tempnam(sys_get_temp_dir(), 'PDOS_PAGE_') . '.docx';
            $tp = new TemplateProcessor($cleanTemplatePath);
            $tp->setMacroChars('{{', '}}');

            // ── TOP HALF ──────────────────────────────────────
            if ($top) {
                $certCounter++;
                $company = $this->getCompany($top);
                $this->fillHalf($tp, 'TOP', $top, $company);
                $this->set($tp, 'CERT_NO_TOP', $this->formatCertNo($baseCertNo, $certCounter, $top));
            } else {
                $this->clearHalf($tp, 'TOP');
                $this->set($tp, 'CERT_NO_TOP', '');
            }

            // ── BOTTOM HALF ───────────────────────────────────
            if ($bottom) {
                $certCounter++;
                $company = $this->getCompany($bottom);
                $this->fillHalf($tp, 'BOTTOM', $bottom, $company);
                $this->set($tp, 'CERT_NO_BOTTOM', $this->formatCertNo($baseCertNo, $certCounter, $bottom));
            } else {
                // Odd applicant (e.g. 3rd person) -> Leave bottom half clean & empty
                $this->clearHalf($tp, 'BOTTOM');
                $this->set($tp, 'CERT_NO_BOTTOM', '');
            }

            // Shared Keys & Dates
            $this->set($tp, 'PDOS_DATE', $formattedDate);
            $this->set($tp, 'PDOS_DATE_TOP', $formattedDate);
            $this->set($tp, 'PDOS_DATE_BOTTOM', $bottom ? $formattedDate : '');
            $this->set($tp, 'CERT_NO', $this->formatCertNo($baseCertNo, max(1, $certCounter - ($bottom ? 1 : 0)), $top));

            $tp->saveAs($pageFile);
            $pageFiles[] = $pageFile;
        }

        @unlink($cleanTemplatePath);

        // Return single page or merge multi-page DOCX
        if (count($pageFiles) === 1) {
            return $pageFiles[0];
        }

        $merged = tempnam(sys_get_temp_dir(), 'PDOS_BATCH_') . '.docx';
        $this->mergeDocxPages($pageFiles, $merged);

        foreach ($pageFiles as $f) {
            @unlink($f);
        }

        return $merged;
    }

    private function fillHalf(TemplateProcessor $tp, string $half, $applicant, ?Company $company): void
    {
        $this->set($tp, "APPLICANT_NAME_{$half}", $this->getApplicantName($applicant));
        $this->set($tp, "COMPANY_CATEGORY_{$half}", $this->getCategoryName($applicant, $company));
        $this->set($tp, "COUNTRY_{$half}", $this->getCountry($company));
        $this->set($tp, "COMPANY_{$half}", $this->getForeignEmployer($company));
        $this->set($tp, "COMPANY_EMPLOYER_{$half}", self::FOREIGN_PRINCIPAL);
        $this->set($tp, "FOREIGN_PRINCIPAL_{$half}", self::FOREIGN_PRINCIPAL);
    }

    private function clearHalf(TemplateProcessor $tp, string $half): void
    {
        foreach ([
            'APPLICANT_NAME', 'COMPANY_CATEGORY', 'COUNTRY', 'COMPANY',
            'COMPANY_EMPLOYER', 'FOREIGN_PRINCIPAL',
        ] as $k) {
            $this->set($tp, "{$k}_{$half}", '');
        }
    }

    private function set(TemplateProcessor $tp, string $key, ?string $value): void
    {
        $tp->setValue($key, $value ?? '');
    }

    /**
     * Repairs template XML before processing to fix split tags and stray brackets like {{COMPANY_CATEGORY_TOP}}]
     */
    private function prepareCleanTemplate(string $templatePath): string
    {
        $cleanTemp = tempnam(sys_get_temp_dir(), 'PDOS_TPL_') . '.docx';
        copy($templatePath, $cleanTemp);

        $zip = new ZipArchive();
        if ($zip->open($cleanTemp) === true) {
            $xml = $zip->getFromName('word/document.xml');
            if ($xml !== false) {
                // 1. Join split braces like { <w:t>{</w:t> -> {{
                $xml = preg_replace('/\{\s*(?:<[^>]+>\s*)*\{/', '{{', $xml);
                $xml = preg_replace('/\}\s*(?:<[^>]+>\s*)*\}/', '}}', $xml);

                // 2. Strip tags & whitespace inside {{ ... }}
                $xml = preg_replace_callback('/\{\{(?:[^}]|<[^>]+>)*\}\}/s', function ($m) {
                    $inner = strip_tags($m[0]);
                    $inner = preg_replace('/[\]\}]+$/', '', $inner);
                    $inner = preg_replace('/^[\[\{]+/', '', $inner);
                    $inner = preg_replace('/\s+/', '', $inner);
                    return '{{' . $inner . '}}';
                }, $xml);

                // 3. Fix stray brackets at end of tags
                $xml = preg_replace('/\{\{([A-Z0-9_]+)[\}\]]+/', '{{\1}}', $xml);

                $zip->addFromString('word/document.xml', $xml);
            }
            $zip->close();
        }

        return $cleanTemp;
    }

    /**
     * Merges multiple single-page DOCX files into one multi-page DOCX file
     */
    private function mergeDocxPages(array $pageFiles, string $outputPath): void
    {
        if (empty($pageFiles)) {
            throw new \RuntimeException('No PDOS pages to merge.');
        }

        copy($pageFiles[0], $outputPath);

        if (count($pageFiles) === 1) {
            return;
        }

        $baseZip = new ZipArchive();
        if ($baseZip->open($outputPath) !== true) {
            throw new \RuntimeException('Cannot open base PDOS docx for merge.');
        }

        $baseXml = $baseZip->getFromName('word/document.xml');
        if ($baseXml === false) {
            $baseZip->close();
            throw new \RuntimeException('word/document.xml missing in base PDOS.');
        }

        if (! preg_match('/<w:body[^>]*>(.*?)<\/w:body>/s', $baseXml, $baseMatch)) {
            $baseZip->close();
            throw new \RuntimeException('Could not parse base document body.');
        }

        $fullBodyContent = $baseMatch[1];
        $sectPr = '';

        if (preg_match('/<w:sectPr\b.*?<\/w:sectPr>/s', $fullBodyContent, $sp)) {
            $sectPr = $sp[0];
            $bodyWithoutSectPr = preg_replace('/<w:sectPr\b.*?<\/w:sectPr>/s', '', $fullBodyContent);
        } else {
            $bodyWithoutSectPr = $fullBodyContent;
        }

        $mergedBody = $bodyWithoutSectPr;

        for ($i = 1; $i < count($pageFiles); $i++) {
            $zip = new ZipArchive();
            if ($zip->open($pageFiles[$i]) !== true) {
                continue;
            }
            $xml = $zip->getFromName('word/document.xml');
            $zip->close();

            if ($xml && preg_match('/<w:body[^>]*>(.*?)<\/w:body>/s', $xml, $m)) {
                $pageContent = preg_replace('/<w:sectPr\b.*?<\/w:sectPr>/s', '', $m[1]);
                $mergedBody .= '<w:p><w:r><w:br w:type="page"/></w:r></w:p>' . $pageContent;
            }
        }

        $mergedBody .= $sectPr;

        $newXml = preg_replace(
            '/<w:body[^>]*>.*?<\/w:body>/s',
            '<w:body>' . $mergedBody . '</w:body>',
            $baseXml
        );

        $baseZip->addFromString('word/document.xml', $newXml);
        $baseZip->close();
    }

    private function getCompany($applicant): ?Company
    {
        if (! $applicant) {
            return null;
        }

        // 1. From internships
        if (isset($applicant->internships) && $applicant->internships->isNotEmpty()) {
            $internship = $applicant->internships->firstWhere('is_current', true)
                ?? $applicant->internships->first();

            $company = $internship?->receivingCompany
                ?? $internship?->acceptingCompany
                ?? $internship?->dispatchingCompany;

            if ($company) {
                if (! $company->relationLoaded('category') && $company->category_id) {
                    $company->load('category');
                }
                return $company;
            }
        }

        // 2. From applicant_batches deployment_company (e.g. ID "383" in JSON)
        $batches = $applicant->applicantBatches ?? $applicant->applicant_batches ?? null;
        if ($batches && $batches->isNotEmpty()) {
            foreach ($batches as $ab) {
                $companyId = $ab->deployment_company ?? $ab->deployment_company_id ?? null;
                if ($companyId && is_numeric($companyId)) {
                    $company = Company::with('category')->find((int) $companyId);
                    if ($company) {
                        return $company;
                    }
                }
                if ($ab->batch && isset($ab->batch->company)) {
                    $company = $ab->batch->company;
                    if (! $company->relationLoaded('category') && $company->category_id) {
                        $company->load('category');
                    }
                    return $company;
                }
            }
        }

        return null;
    }

    private function getApplicantName($applicant): string
    {
        if (! $applicant) {
            return '';
        }

        $name = $applicant->full_name
            ?? trim(implode(' ', array_filter([
                $applicant->first_name ?? null,
                $applicant->middle_name ?? null,
                $applicant->last_name ?? null,
                $applicant->suffix ?? null,
            ])));

        return mb_strtoupper($name ?: '');
    }

    private function getCategoryName($applicant, ?Company $company): string
    {
        // 🎯 1. Primary: Company Category Name (e.g. "FRAME WORKING", "CAREGIVING", etc.)
        $categoryName = $company?->category?->name
            ?? $company?->category_name
            ?? null;

        if (! empty($categoryName)) {
            return mb_strtoupper(trim($categoryName));
        }

        // 2. Secondary: Internship Job Description
        $internship = null;
        if (isset($applicant->internships) && $applicant->internships->isNotEmpty()) {
            $internship = $applicant->internships->firstWhere('is_current', true)
                ?? $applicant->internships->first();
        }

        if ($internship?->job_description) {
            return mb_strtoupper(trim($internship->job_description));
        }

        // 3. Fallback: Applicant attributes
        $fallback = $applicant->applied_position
            ?? $applicant->skill_category
            ?? $applicant->trade_or_occupation
            ?? $applicant->skill
            ?? 'TECHNICAL INTERN';

        return mb_strtoupper(trim((string) $fallback));
    }

    private function getCountry(?Company $company): string
    {
        $country = $company?->country
            ?? $company?->destination_country
            ?? $company?->country_name
            ?? 'JAPAN';

        return mb_strtoupper(trim((string) $country));
    }

    private function getForeignEmployer(?Company $company): string
    {
        if (! $company) {
            return 'N/A';
        }

        $name = $company->name_on_document
            ?? $company->name
            ?? $company->name_japanese
            ?? $company->company_name
            ?? null;

        return ! empty($name) ? mb_strtoupper(trim($name)) : 'N/A';
    }

    private function formatCertNo(?string $baseCertNo, int $index, $applicant = null): string
    {
        if ($applicant && ! empty($applicant->pdos_number)) {
            return trim((string) $applicant->pdos_number);
        }

        if ($baseCertNo === null || trim($baseCertNo) === '') {
            if ($applicant && isset($applicant->id)) {
                return 'TRI' . now()->format('Y') . '_' . str_pad((string) $applicant->id, 5, '0', STR_PAD_LEFT);
            }
            return 'TRI' . now()->format('Y') . '_' . str_pad((string) $index, 5, '0', STR_PAD_LEFT);
        }

        $baseCertNo = trim($baseCertNo);
        if (preg_match('/^(.*?)(\d+)$/', $baseCertNo, $m)) {
            $prefix = $m[1];
            $number = (int) $m[2];
            $len = strlen($m[2]);
            $next = $number + ($index - 1);
            return $prefix . str_pad((string) $next, $len, '0', STR_PAD_LEFT);
        }

        return $index <= 1 ? $baseCertNo : $baseCertNo . '-' . $index;
    }
}