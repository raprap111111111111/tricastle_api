<?php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class CreateApplicantBatchDTO
{
    public function __construct(
        public string $returnReason,
        public ?int   $processedBy = null,
    ) {}
}