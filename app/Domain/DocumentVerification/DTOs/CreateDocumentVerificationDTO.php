<?php

namespace App\Domain\DocumentVerification\DTOs;

final readonly class CreateDocumentVerificationDTO
{
    public function __construct(
        public int    $applicantDocumentId,
        public ?int   $verifiedBy        = null,
        public ?array $verificationData  = null,
        public ?array $sourceData        = null,
        public ?string $notes            = null,
    ) {}
}