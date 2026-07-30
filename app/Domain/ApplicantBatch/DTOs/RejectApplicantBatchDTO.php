<?php

// app/Domain/ApplicantBatch/DTOs/RejectApplicantBatchDTO.php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class RejectApplicantBatchDTO
{
    public function __construct(
        public string $rejectionReason,
        public ?int   $processedBy = null,
    ) {}
}