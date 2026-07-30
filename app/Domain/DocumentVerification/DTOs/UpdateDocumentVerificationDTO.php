<?php

namespace App\Domain\DocumentVerification\DTOs;

final readonly class UpdateDocumentVerificationDTO
{
    public function __construct(
        public ?array  $verificationData = null,
        public ?array  $sourceData       = null,
        public ?int    $totalFields      = null,
        public ?int    $matchedFields    = null,
        public ?int    $mismatchedFields = null,
        public ?int    $missingFields    = null,
        public ?string $notes            = null,
        public ?int    $reviewedBy       = null,
    ) {}
}