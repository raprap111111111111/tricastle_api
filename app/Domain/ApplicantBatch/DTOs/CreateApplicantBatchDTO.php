<?php

// app/Domain/ApplicantBatch/DTOs/CreateApplicantBatchDTO.php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class CreateApplicantBatchDTO
{
    public function __construct(
        public int     $applicantId,
        public int     $batchId,
        public string  $status = 'applied',
        public ?string $appliedAt = null,
        public ?int    $processedBy = null,
    ) {}
}