<?php

namespace App\Domain\CorrectionApproval\DTOs;

final readonly class CreateCorrectionApprovalDTO
{
    public function __construct(
        public int    $correctionRequestId,
        public int    $approverId,
        public int    $approvalLevel  = 1,
        public string $decision       = 'pending',
        public ?string $comments      = null,
        public ?array  $conditions    = null,
    ) {}
}