<?php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class CreateApplicantBatchDTO
{
    public function __construct(
        public int  $applicantId,
        public int  $batchId,
        public ?int $processedBy = null,
    ) {}
}