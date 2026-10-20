<?php

namespace App\Domain\Applicant\Actions;

use App\Models\Applicant;
use Illuminate\Support\Collection;
use PhpOffice\PhpWord\TemplateProcessor;

class GeneratePdosBatchCertificateAction
{
    /**
     * Generate a single PDOS Word Document with 2 certificates per page.
     * Automatically handles 1, 10, 40, or 100+ applicants!
     *
     * @param  Collection<Applicant>  $applicants
     * @param  array  $options
     * @return string Path to generated temporary .docx file
     */
    public function execute(Collection $applicants, array $options = []): string
    {
        $templatePath = resource_path('templates/PDOS.docx');

        if (! file_exists($templatePath)) {
            throw new \RuntimeException("PDOS template not found at: {$templatePath}");
        }

        $templateProcessor = new TemplateProcessor($templatePath);

        $pdosDate = $options['pdos_date'] ?? now()->format('F j, Y');

        // Group all selected applicants into pairs of 2 (Top and Bottom)
        $pages = $applicants->chunk(2)->values();
        $totalPages = $pages->count();

        // Clone the page block for as many pages as needed
        $templateProcessor->cloneBlock('PAGE_BLOCK', $totalPages, true, true);

        // Fill each page pair
        foreach ($pages as $pageIndex => $pair) {
            $blockNumber = $pageIndex + 1; // 1-based index (#1, #2, #3...)

            $topApplicant    = $pair->get(0);
            $bottomApplicant = $pair->get(1);

            // Fill Top Certificate
            if ($topApplicant) {
                $this->fillSlot($templateProcessor, 'TOP', $blockNumber, $topApplicant, $pdosDate);
            } else {
                $this->clearSlot($templateProcessor, 'TOP', $blockNumber);
            }

            // Fill Bottom Certificate
            if ($bottomApplicant) {
                $this->fillSlot($templateProcessor, 'BOTTOM', $blockNumber, $bottomApplicant, $pdosDate);
            } else {
                $this->clearSlot($templateProcessor, 'BOTTOM', $blockNumber);
            }
        }

        // Save output to temp directory
        $outputDir = storage_path('app/temp');
        if (! is_dir($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $fileName = 'PDOS_BATCH_' . count($applicants) . '_PAX_' . time() . '.docx';
        $outputPath = $outputDir . '/' . $fileName;

        $templateProcessor->saveAs($outputPath);

        return $outputPath;
    }

    private function fillSlot(
        TemplateProcessor $templateProcessor,
        string $position,
        int $blockNumber,
        Applicant $applicant,
        string $pdosDate
    ): void {
        $fullName  = strtoupper(trim("{$applicant->first_name} {$applicant->middle_name} {$applicant->last_name} {$applicant->suffix}"));
        $category  = strtoupper($applicant->trade_or_occupation ?? $applicant->skill_category ?? 'REINFORCING BAR CONSTRUCTION WORK');
        $country   = 'JAPAN';
        $company   = 'MULTI CONTRACTORS COOPERATIVE';
        $principal = 'ISHIZAWA KOGYO';
        $certNo    = 'TRI' . now()->format('Y') . '_' . str_pad((string) $applicant->id, 5, '0', STR_PAD_LEFT);

        $templateProcessor->setValue("APPLICANT_NAME_{$position}#{$blockNumber}", $fullName);
        $templateProcessor->setValue("COMPANY_CATEGORY_{$position}#{$blockNumber}", $category);
        $templateProcessor->setValue("COUNTRY_{$position}#{$blockNumber}", $country);
        $templateProcessor->setValue("COMPANY_{$position}#{$blockNumber}", $company);
        $templateProcessor->setValue("COMPANY_EMPLOYER_{$position}#{$blockNumber}", $principal);
        $templateProcessor->setValue("PDOS_DATE_{$position}#{$blockNumber}", $pdosDate);
        $templateProcessor->setValue("CERT_NO_{$position}#{$blockNumber}", $certNo);
    }

    private function clearSlot(TemplateProcessor $templateProcessor, string $position, int $blockNumber): void
    {
        $templateProcessor->setValue("APPLICANT_NAME_{$position}#{$blockNumber}", '');
        $templateProcessor->setValue("COMPANY_CATEGORY_{$position}#{$blockNumber}", '');
        $templateProcessor->setValue("COUNTRY_{$position}#{$blockNumber}", '');
        $templateProcessor->setValue("COMPANY_{$position}#{$blockNumber}", '');
        $templateProcessor->setValue("COMPANY_EMPLOYER_{$position}#{$blockNumber}", '');
        $templateProcessor->setValue("PDOS_DATE_{$position}#{$blockNumber}", '');
        $templateProcessor->setValue("CERT_NO_{$position}#{$blockNumber}", '');
    }
}