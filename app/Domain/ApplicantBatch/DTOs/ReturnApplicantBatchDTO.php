<?php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class ReturnApplicantBatchDTO
{
    public function __construct(
        public string $returnReason,
        public ?int   $processedBy = null,
    ) {}
}