<?php

namespace App\Domain\CorrectionRequest\DTOs;

final readonly class UpdateCorrectionRequestDTO
{
    public function __construct(
        public ?string $description          = null,
        public ?string $severity             = null,
        public ?array  $fieldsToCorrect      = null,
        public ?array  $correctionData       = null,
        public ?string $justification        = null,
        public ?bool   $requiresApproval     = null,
        public ?bool   $requiresNewDocument  = null,
        public ?string $dueDate              = null,
    ) {}
}