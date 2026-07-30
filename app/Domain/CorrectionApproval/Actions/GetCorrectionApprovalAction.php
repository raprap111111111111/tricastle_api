<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;
use App\Models\CorrectionApproval;

class GetCorrectionApprovalAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(int $id): CorrectionApproval
    {
        return $this->repository->findOrFail($id);
    }
}