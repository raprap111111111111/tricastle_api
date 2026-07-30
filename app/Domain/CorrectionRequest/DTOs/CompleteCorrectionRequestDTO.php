<?php

namespace App\Domain\CorrectionRequest\DTOs;

final readonly class CompleteCorrectionRequestDTO
{
    public function __construct(
        public int     $completedBy,
        public ?array  $correctionData = null,
        public ?string $notes          = null,
    ) {}
}