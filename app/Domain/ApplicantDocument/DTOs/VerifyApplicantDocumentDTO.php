<?php

namespace App\Domain\ApplicantDocument\DTOs;

final readonly class VerifyApplicantDocumentDTO
{
    public function __construct(
        public int     $verifiedBy,
        public ?string $notes         = null,
        public ?array  $validatedData = null,
    ) {}
}