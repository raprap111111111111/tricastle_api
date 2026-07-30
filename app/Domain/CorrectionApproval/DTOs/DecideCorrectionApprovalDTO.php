<?php

namespace App\Domain\CorrectionApproval\DTOs;

final readonly class DecideCorrectionApprovalDTO
{
    public function __construct(
        public string  $decision,
        public int     $approverId,
        public ?string $comments   = null,
        public ?array  $conditions = null,
    ) {}
}