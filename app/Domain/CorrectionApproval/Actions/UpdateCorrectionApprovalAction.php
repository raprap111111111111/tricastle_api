<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\DTOs\UpdateCorrectionApprovalDTO;
use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;
use App\Models\CorrectionApproval;

class UpdateCorrectionApprovalAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(CorrectionApproval $approval, UpdateCorrectionApprovalDTO $dto): CorrectionApproval
    {
        return $this->repository->update($approval->id, array_filter([
            'comments'       => $dto->comments,
            'conditions'     => $dto->conditions,
            'approval_level' => $dto->approvalLevel,
        ], fn ($value) => $value !== null));
    }
}