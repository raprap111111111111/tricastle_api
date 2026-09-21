<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Services\InsuranceFormGeneratorService;
use App\Models\Applicant;
use InvalidArgumentException;

class ExportInsuranceFormAction
{
    public function __construct(
        private readonly InsuranceFormGeneratorService $generatorService
    ) {}

    public function execute(mixed $applicantsPayload, string $departureDate): array
    {
        $ids = [];
        $frontendData = [];

        if (is_array($applicantsPayload) && ! empty($applicantsPayload)) {
            if (isset($applicantsPayload[0]) && is_array($applicantsPayload[0])) {
                // Option B: Array of objects containing field overrides
                $ids = array_column($applicantsPayload, 'id');
                $frontendData = $applicantsPayload;
            } else {
                // Option A: Array of integer IDs
                $ids = $applicantsPayload;
            }
        }

        if (empty($ids)) {
            throw new InvalidArgumentException('No valid applicant IDs provided for export.');
        }

        $applicants = Applicant::with(['batches.company', 'applicantBatches.batch.company'])
            ->whereIn('id', $ids)
            ->get();

        if ($applicants->isEmpty()) {
            throw new InvalidArgumentException('No valid applicants found for export.');
        }

        // Preserve input order
        $orderedApplicants = collect($ids)
            ->map(fn ($id) => $applicants->firstWhere('id', $id))
            ->filter();

        return $this->generatorService->generate($orderedApplicants, $departureDate, $frontendData);
    }
}