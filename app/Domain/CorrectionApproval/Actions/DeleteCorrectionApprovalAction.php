<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;
use App\Models\CorrectionApproval;

class DeleteCorrectionApprovalAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(CorrectionApproval $approval): void
    {
        $this->repository->delete($approval->id);
    }
}