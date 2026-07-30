<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Enums\ApplicantBatchStatus;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class WithdrawApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, ?int $processedBy = null): ApplicantBatch
    {
        return $this->repository->update($applicantBatch->id, array_filter([
            'status'       => ApplicantBatchStatus::WITHDRAWN->value,
            'processed_by' => $processedBy,
        ], fn ($value) => $value !== null));
    }
}