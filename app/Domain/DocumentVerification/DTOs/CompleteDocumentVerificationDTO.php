<?php

namespace App\Domain\DocumentVerification\DTOs;

final readonly class CompleteDocumentVerificationDTO
{
    public function __construct(
        public int    $verifiedBy,
        public int    $totalFields,
        public int    $matchedFields,
        public int    $mismatchedFields,
        public int    $missingFields,
        public ?array $verificationData  = null,
        public ?array $sourceData        = null,
        public ?string $notes            = null,
    ) {}
}