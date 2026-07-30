<?php

namespace App\Domain\Batch\DTOs;

final readonly class CreateBatchDTO
{
    public function __construct(
        public int     $batchNumber,
        public string  $name,
        public ?string $country        = null,
        public ?string $deploymentDate = null,
        public string  $status         = 'draft',
        public bool    $isActive       = false,
        public ?string $description    = null,
    ) {}
}