<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\RejectApplicantBatchDTO;
use App\Enums\ApplicantBatchStatus;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class RejectApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, RejectApplicantBatchDTO $dto): ApplicantBatch
    {
        return $this->repository->update($applicantBatch->id, array_filter([
            'status'           => ApplicantBatchStatus::REJECTED->value,
            'rejection_reason' => $dto->rejectionReason,
            'processed_by'     => $dto->processedBy,
        ], fn ($value) => $value !== null));
    }
}