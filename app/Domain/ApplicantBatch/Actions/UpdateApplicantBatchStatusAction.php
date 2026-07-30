<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\DTOs\UpdateApplicantBatchStatusDTO;
use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class UpdateApplicantBatchStatusAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch, UpdateApplicantBatchStatusDTO $dto): ApplicantBatch
    {
        return $this->repository->update($applicantBatch->id, array_filter([
            'status'       => $dto->status,
            'processed_by' => $dto->processedBy,
        ], fn ($value) => $value !== null));
    }
}