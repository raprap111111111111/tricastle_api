<?php

// app/Domain/ApplicantBatch/DTOs/UpdateApplicantBatchStatusDTO.php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class UpdateApplicantBatchStatusDTO
{
    public function __construct(
        public string $status,
        public ?int   $processedBy = null,
    ) {}
}