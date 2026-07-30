<?php

namespace App\Domain\CorrectionApproval\DTOs;

final readonly class UpdateCorrectionApprovalDTO
{
    public function __construct(
        public ?string $comments   = null,
        public ?array  $conditions = null,
        public ?int    $approvalLevel = null,
    ) {}
}