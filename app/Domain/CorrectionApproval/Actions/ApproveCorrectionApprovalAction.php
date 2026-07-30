<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\DTOs\DecideCorrectionApprovalDTO;
use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;
use App\Models\CorrectionApproval;

class ApproveCorrectionApprovalAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(CorrectionApproval $approval, DecideCorrectionApprovalDTO $dto): CorrectionApproval
    {
        return $this->repository->update($approval->id, array_filter([
            'decision'    => 'approved',
            'approver_id' => $dto->approverId,
            'comments'    => $dto->comments,
            'conditions'  => $dto->conditions,
            'decided_at'  => now(),
        ], fn ($value) => $value !== null));
    }
}