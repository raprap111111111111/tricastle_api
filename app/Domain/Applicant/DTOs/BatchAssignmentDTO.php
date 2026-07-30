<?php

namespace App\Domain\Applicant\DTOs;

final readonly class BatchAssignmentDTO
{
    public function __construct(
        public int    $batchId,
        public string $status      = 'applied',
        public ?int   $processedBy = null,
    ) {}
}