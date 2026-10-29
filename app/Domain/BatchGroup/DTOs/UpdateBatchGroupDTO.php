<?php

namespace App\Domain\BatchGroup\DTOs;

final readonly class UpdateBatchGroupDTO
{
    public function __construct(
        public ?string $name     = null,
        public ?string $color    = null,
        public ?int    $maxSlots = null,
    ) {}
}