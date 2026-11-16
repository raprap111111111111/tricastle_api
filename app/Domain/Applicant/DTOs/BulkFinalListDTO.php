<?php

namespace App\Domain\Applicant\DTOs;

class BulkFinalListDTO
{
    public function __construct(
        public readonly array $ids,
        public readonly ?int $reviewedBy = null,
    ) {}
}