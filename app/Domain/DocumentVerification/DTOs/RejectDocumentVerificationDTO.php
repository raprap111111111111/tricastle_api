<?php

namespace App\Domain\DocumentVerification\DTOs;

final readonly class RejectDocumentVerificationDTO
{
    public function __construct(
        public string  $rejectionReason,
        public int     $reviewedBy,
        public ?string $notes = null,
    ) {}
}