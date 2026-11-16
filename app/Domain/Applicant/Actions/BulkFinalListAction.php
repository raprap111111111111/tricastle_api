<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;

class BulkFinalListAction
{
    public function __construct(
        private readonly ApplicantRepository $repository,
    ) {}

    public function execute(array $ids, ?int $reviewedBy = null): int
    {
        return $this->repository->bulkMoveToFinalList($ids, $reviewedBy);
    }
}