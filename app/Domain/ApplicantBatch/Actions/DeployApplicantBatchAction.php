<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Enums\ApplicantBatchStatus;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;
use RuntimeException;

class DeployApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, ?int $processedBy = null): ApplicantBatch
    {
        if ($applicantBatch->status !== ApplicantBatchStatus::ACCEPTED) {
            throw new RuntimeException('Only accepted applicants can be deployed.');
        }

        return $this->repository->update($applicantBatch->id, array_filter([
            'status'       => ApplicantBatchStatus::DEPLOYED->value,
            'deployed_at'  => now()->toDateString(),
            'processed_by' => $processedBy,
        ], fn ($value) => $value !== null));
    }
}