<?php

namespace App\Domain\ApplicantDocument\DTOs;

final readonly class RejectApplicantDocumentDTO
{
    public function __construct(
        public string $rejectionReason,
        public int    $rejectedBy,
        public ?string $notes = null,
    ) {}
}