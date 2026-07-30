<?php

// app/Domain/ApplicantBatch/DTOs/UpdateApplicantBatchDTO.php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class UpdateApplicantBatchDTO
{
    public function __construct(
        public ?string $status = null,
        public ?string $interviewDate = null,
        public ?string $medicalDate = null,
        public ?string $examDate = null,
        public ?string $acceptedAt = null,
        public ?string $deployedAt = null,
        public ?float  $examScore = null,
        public ?string $interviewNotes = null,
        public ?string $medicalNotes = null,
        public ?string $rejectionReason = null,
        public ?int    $processedBy = null,
    ) {}
}