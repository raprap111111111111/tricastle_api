<?php

namespace App\Domain\Batch\DTOs;

final readonly class UpdateBatchDTO
{
    public function __construct(
        public ?int    $batchNumber    = null,
        public ?string $name           = null,
        public ?string $country        = null,
        public ?string $deploymentDate = null,
        public ?string $status         = null,
        public ?bool   $isActive       = null,
        public ?string $description    = null,
    ) {}
}