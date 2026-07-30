<?php

namespace App\Domain\VerificationMismatch\DTOs;

final readonly class ResolveVerificationMismatchDTO
{
    public function __construct(
        public string  $status,
        public int     $resolvedBy,
        public ?string $resolutionNotes = null,
    ) {}
}