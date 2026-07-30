<?php

namespace App\Domain\VerificationMismatch\DTOs;

final readonly class CreateVerificationMismatchDTO
{
    public function __construct(
        public int     $documentVerificationId,
        public string  $fieldName,
        public string  $fieldLabel,
        public ?string $sourceValue   = null,
        public ?string $enteredValue  = null,
        public string  $severity      = 'low',
        public string  $mismatchType  = 'value_mismatch',
        public string  $status        = 'open',
    ) {}
}