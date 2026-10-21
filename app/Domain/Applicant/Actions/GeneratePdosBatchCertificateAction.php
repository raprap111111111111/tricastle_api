<?php

namespace App\Domain\Applicant\Actions;

use App\Models\Company;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\TemplateProcessor;
use ReflectionClass;

class GeneratePdosBatchCertificateAction
{
    /** Static Foreign Principal (matches your official certificate layout) */
    private const FOREIGN_PRINCIPAL = 'MULTI CONTRACTORS COOPERATIVE';

    public function execute(Collection $applicants, array $options = []): string
    {
        $templatePath = resource_path('templates/PDOS.docx');

        if (! file_exists($templatePath)) {
            // fallback if template was placed under storage
            $alt = storage_path('app/templates/PDOS.docx');
            if (file_exists($alt)) {
                $templatePath = $alt;
            } else {
                throw new \RuntimeException("PDOS template not found at {$templatePath}");
            }
        }

        $formattedDate = ! empty($options['pdos_date'])
            ? date('F j, Y', strtotime($options['pdos_date']))
            : now()->format('F j, Y');

        $baseCertNo = $options['cert_no'] ?? null;

        $tempFile = tempnam(sys_get_temp_dir(), 'PDOS_BATCH_') . '.docx';

        $templateProcessor = new TemplateProcessor($templatePath);

        // Clean Word-split {{placeholders}} before macro setup
        $this->sanitizeMainDocumentXml($templateProcessor);
        $templateProcessor->setMacroChars('{{', '}}');

        $chunks     = $applicants->chunk(2)->values();
        $totalPages = max(1, $chunks->count());

        // If template has a cloneable block named "certificate_page", duplicate per pair.
        // Safe no-op when the block is absent.
        try {
            $templateProcessor->cloneBlock('certificate_page', $totalPages, true, true);
        } catch (\Throwable) {
            // Template is a single page with _TOP / _BOTTOM placeholders only
        }

        $certCounter = 0;

        for ($i = 0; $i < $totalPages; $i++) {
            $pair   = $chunks->get($i)?->values() ?? collect();
            $top    = $pair->get(0);
            $bottom = $pair->get(1);

            // Suffix used when cloneBlock adds #1, #2, ...
            $s = $totalPages > 1 ? '#' . ($i + 1) : '';

            // ─── TOP ────────────────────────────────────────────────────────
            if ($top) {
                $certCounter++;
                $company = $this->getCompany($top);

                $this->set($templateProcessor, 'APPLICANT_NAME_TOP' . $s, $this->getApplicantName($top));
                $this->set($templateProcessor, 'COMPANY_CATEGORY_TOP' . $s, $this->getCategoryName($top, $company));
                $this->set($templateProcessor, 'COUNTRY_TOP' . $s, $this->getCountry($company));
                $this->set($templateProcessor, 'COMPANY_TOP' . $s, $this->getForeignEmployer($company));
                // Static principal (and keep alias if old template still has it)
                $this->set($templateProcessor, 'COMPANY_EMPLOYER_TOP' . $s, self::FOREIGN_PRINCIPAL);
                $this->set($templateProcessor, 'FOREIGN_PRINCIPAL_TOP' . $s, self::FOREIGN_PRINCIPAL);
            } else {
                foreach (['APPLICANT_NAME_TOP', 'COMPANY_CATEGORY_TOP', 'COUNTRY_TOP', 'COMPANY_TOP', 'COMPANY_EMPLOYER_TOP', 'FOREIGN_PRINCIPAL_TOP'] as $k) {
                    $this->set($templateProcessor, $k . $s, '');
                }
            }

            // ─── BOTTOM ─────────────────────────────────────────────────────
            if ($bottom) {
                $certCounter++;
                $company = $this->getCompany($bottom);

                $this->set($templateProcessor, 'APPLICANT_NAME_BOTTOM' . $s, $this->getApplicantName($bottom));
                $this->set($templateProcessor, 'COMPANY_CATEGORY_BOTTOM' . $s, $this->getCategoryName($bottom, $company));
                $this->set($templateProcessor, 'COUNTRY_BOTTOM' . $s, $this->getCountry($company));
                $this->set($templateProcessor, 'COMPANY_BOTTOM' . $s, $this->getForeignEmployer($company));
                $this->set($templateProcessor, 'COMPANY_EMPLOYER_BOTTOM' . $s, self::FOREIGN_PRINCIPAL);
                $this->set($templateProcessor, 'FOREIGN_PRINCIPAL_BOTTOM' . $s, self::FOREIGN_PRINCIPAL);
            } else {
                foreach (['APPLICANT_NAME_BOTTOM', 'COMPANY_CATEGORY_BOTTOM', 'COUNTRY_BOTTOM', 'COMPANY_BOTTOM', 'COMPANY_EMPLOYER_BOTTOM', 'FOREIGN_PRINCIPAL_BOTTOM'] as $k) {
                    $this->set($templateProcessor, $k . $s, '');
                }
            }

            // ─── SHARED (date + cert) ────────────────────────────────────────
            $this->set($templateProcessor, 'PDOS_DATE' . $s, $formattedDate);
            $this->set($templateProcessor, 'CERT_NO' . $s, $this->formatCertNo($baseCertNo, $certCounter));
        }

        // Also set unsuffixed keys so single-page templates always fill
        if ($totalPages === 1) {
            // already set above without suffix
        }

        $templateProcessor->saveAs($tempFile);

        return $tempFile;
    }

    /** setValue wrapper — never passes null */
    private function set(TemplateProcessor $tp, string $key, ?string $value): void
    {
        $tp->setValue($key, $value ?? '');
    }

    /**
     * Aggressively repair Word-split placeholders inside document.xml
     * e.g. {{COM PANY_...}} or {{...}} broken across <w:t> runs
     */
    private function sanitizeMainDocumentXml(TemplateProcessor $templateProcessor): void
    {
        $reflection = new ReflectionClass($templateProcessor);
        if (! $reflection->hasProperty('tempDocumentMainPart')) {
            return;
        }

        $property = $reflection->getProperty('tempDocumentMainPart');
        $property->setAccessible(true);
        $xml = $property->getValue($templateProcessor);

        if (! is_string($xml)) {
            return;
        }

        // Join braces split across tags: { <w:t>{</w:t> → {{
        $xml = preg_replace('/\{\s*(?:<[^>]+>\s*)+\{/', '{{', $xml);
        $xml = preg_replace('/\}\s*(?:<[^>]+>\s*)+\}/', '}}', $xml);

        // Strip any XML tags and whitespace inside {{ ... }}
        $xml = preg_replace_callback('/\{\{(.*?)\}\}/s', function ($m) {
            $inner = strip_tags($m[1]);
            $inner = preg_replace('/\s+/', '', $inner); // COMPANY_ CATEGORY → COMPANY_CATEGORY
            return '{{' . $inner . '}}';
        }, $xml);

        $property->setValue($templateProcessor, $xml);
    }

    /**
     * Resolve company from batch first, then current internship receiving company
     */
    private function getCompany($applicant): ?Company
    {
        if (! $applicant) {
            return null;
        }

        // 1) Final-list / batch assignment
        $batches = $applicant->applicantBatches ?? $applicant->applicant_batches ?? null;
        if ($batches && $batches->isNotEmpty()) {
            $company = $batches->first()?->batch?->company;
            if ($company) {
                return $company;
            }
        }

        // 2) Current internship receiving company
        $internship = null;
        if (isset($applicant->internships)) {
            $internship = $applicant->internships->firstWhere('is_current', true)
                ?? $applicant->internships->first();
        } elseif (method_exists($applicant, 'internships')) {
            $internship = $applicant->internships()->where('is_current', true)->first()
                ?? $applicant->internships()->latest('id')->first();
        }

        if ($internship) {
            return $internship->receivingCompany
                ?? $internship->acceptingCompany
                ?? $internship->dispatchingCompany
                ?? null;
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

    /**
     * Skill / Occupation ← Company Category name
     * fallback: applied_position / skill_category / trade_or_occupation
     */
    private function getCategoryName($applicant, ?Company $company): string
    {
        $categoryName = $company?->category?->name
            ?? $company?->category_name
            ?? null;

        if (! empty($categoryName)) {
            return mb_strtoupper(trim($categoryName));
        }

        $fallback = $applicant->applied_position
            ?? $applicant->skill_category
            ?? $applicant->trade_or_occupation
            ?? $applicant->titp_occupation
            ?? null;

        return mb_strtoupper(trim((string) ($fallback ?: 'WORKER')));
    }

    private function getCountry(?Company $company): string
    {
        $country = $company->country
            ?? $company->destination_country
            ?? $company->country_name
            ?? null;

        return mb_strtoupper(trim((string) ($country ?: 'JAPAN')));
    }

    /** Foreign Employer ← company name on document */
    private function getForeignEmployer(?Company $company): string
    {
        if (! $company) {
            return 'N/A';
        }

        $name = $company->name_on_document
            ?? $company->name
            ?? $company->company_name
            ?? null;

        return ! empty($name) ? mb_strtoupper(trim($name)) : 'N/A';
    }

    private function formatCertNo(?string $baseCertNo, int $index): string
    {
        if ($baseCertNo === null || trim($baseCertNo) === '') {
            return '';
        }

        $baseCertNo = trim($baseCertNo);

        if (preg_match('/^(.*?)(\d+)$/', $baseCertNo, $m)) {
            $prefix = $m[1];
            $number = (int) $m[2];
            $len    = strlen($m[2]);
            $next   = $number + ($index - 1);

            return $prefix . str_pad((string) $next, $len, '0', STR_PAD_LEFT);
        }

        // Non-numeric cert: same value on every cert, or append -N
        return $index <= 1 ? $baseCertNo : $baseCertNo . '-' . $index;
    }
}
