<?php

namespace App\Domain\CorrectionRequest\DTOs;

final readonly class ApproveCorrectionRequestDTO
{
    public function __construct(
        public int     $approvedBy,
        public ?string $notes    = null,
        public ?string $dueDate  = null,
    ) {}
}