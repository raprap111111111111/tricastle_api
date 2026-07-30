<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\DTOs\DecideCorrectionApprovalDTO;
use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;
use App\Models\CorrectionApproval;

class EscalateCorrectionApprovalAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(CorrectionApproval $approval, DecideCorrectionApprovalDTO $dto): CorrectionApproval
    {
        return $this->repository->update($approval->id, array_filter([
            'decision'       => 'escalated',
            'approver_id'    => $dto->approverId,
            'comments'       => $dto->comments,
            'decided_at'     => now(),
            // Escalate to next level
            'approval_level' => $approval->approval_level + 1,
        ], fn ($value) => $value !== null));
    }
}