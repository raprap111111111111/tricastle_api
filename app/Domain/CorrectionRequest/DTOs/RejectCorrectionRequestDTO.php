<?php

namespace App\Domain\CorrectionRequest\DTOs;

final readonly class RejectCorrectionRequestDTO
{
    public function __construct(
        public int     $rejectedBy,
        public string  $reason,
        public ?string $notes = null,
    ) {}
}