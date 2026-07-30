<?php

namespace App\Domain\ApplicantDocument\DTOs;

final readonly class UpdateApplicantDocumentDTO
{
    public function __construct(
        public ?string $documentDate  = null,
        public ?string $expiryDate    = null,
        public ?string $priority      = null,
        public ?string $notes         = null,
        public ?array  $metadata      = null,
        public ?array  $validatedData = null,
    ) {}
}