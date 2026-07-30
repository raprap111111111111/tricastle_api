<?php

namespace App\Domain\ApplicantBatch\Actions;

use App\Domain\ApplicantBatch\Repositories\ApplicantBatchRepository;
use App\Models\ApplicantBatch;

class DeleteApplicantBatchAction
{
    public function __construct(
        private readonly ApplicantBatchRepository $repository
    ) {}

    public function execute(ApplicantBatch $applicantBatch): bool
    {
        return $this->repository->delete($applicantBatch->id);
    }
}