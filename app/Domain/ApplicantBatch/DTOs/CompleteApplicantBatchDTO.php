<?php

namespace App\Domain\ApplicantBatch\DTOs;

final readonly class CompleteApplicantBatchDTO
{
    public function __construct(
        public ?string $completionNotes = null,
        public ?int    $processedBy = null,
    ) {}
}