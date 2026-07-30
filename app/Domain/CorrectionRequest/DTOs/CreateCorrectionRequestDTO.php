<?php

namespace App\Domain\CorrectionRequest\DTOs;

final readonly class CreateCorrectionRequestDTO
{
    public function __construct(
        public int     $documentVerificationId,
        public int     $applicantDocumentId,
        public int     $requestedBy,
        public string  $description,
        public string  $severity              = 'low',
        public ?array  $fieldsToCorrect       = null,
        public ?array  $correctionData        = null,
        public ?string $justification         = null,
        public bool    $requiresApproval      = false,
        public bool    $requiresNewDocument   = false,
        public ?string $dueDate               = null,
    ) {}
}