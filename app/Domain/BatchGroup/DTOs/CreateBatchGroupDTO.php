<?php

namespace App\Domain\BatchGroup\DTOs;

final readonly class CreateBatchGroupDTO
{
    public function __construct(
        public int     $batchId,
        public string  $name,
        public ?string $color    = 'blue',
        public ?int    $maxSlots = null,
    ) {}
}