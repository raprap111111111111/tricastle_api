<?php

namespace App\Domain\OcrJob\DTOs;

final readonly class CancelOcrJobDTO
{
    public function __construct(
        public ?string $notes = null,
    ) {}
}