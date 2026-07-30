<?php

namespace App\Domain\VerificationMismatch\DTOs;

final readonly class UpdateVerificationMismatchDTO
{
    public function __construct(
        public ?string $fieldName        = null,
        public ?string $fieldLabel       = null,
        public ?string $sourceValue      = null,
        public ?string $enteredValue     = null,
        public ?string $severity         = null,
        public ?string $mismatchType     = null,
        public ?string $status           = null,
        public ?string $resolutionNotes  = null,
    ) {}
}