<?php

namespace App\Domain\CorrectionApproval\Actions;

use App\Domain\CorrectionApproval\DTOs\CreateCorrectionApprovalDTO;
use App\Domain\CorrectionApproval\Repositories\CorrectionApprovalRepository;
use App\Models\CorrectionApproval;

class CreateCorrectionApprovalAction
{
    public function __construct(
        private readonly CorrectionApprovalRepository $repository
    ) {}

    public function execute(CreateCorrectionApprovalDTO $dto): CorrectionApproval
    {
        return $this->repository->create([
            'correction_request_id' => $dto->correctionRequestId,
            'approver_id'           => $dto->approverId,
            'approval_level'        => $dto->approvalLevel,
            'decision'              => $dto->decision,
            'comments'              => $dto->comments,
            'conditions'            => $dto->conditions,
        ]);
    }
}