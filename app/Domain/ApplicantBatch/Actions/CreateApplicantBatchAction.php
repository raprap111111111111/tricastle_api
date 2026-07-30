<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\CreateApplicantBatchDTO;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class CreateApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(CreateApplicantBatchDTO $dto): ApplicantBatch
    {
        return $this->repository->create(array_filter([
            'applicant_id' => $dto->applicantId,
            'batch_id'     => $dto->batchId,
            'status'       => $dto->status,
            'applied_at'   => $dto->appliedAt ?? now()->toDateString(),
            'processed_by' => $dto->processedBy,
        ], fn ($value) => $value !== null));
    }
}