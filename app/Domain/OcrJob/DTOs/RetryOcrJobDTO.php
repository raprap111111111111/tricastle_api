<?php

namespace App\Domain\OcrJob\DTOs;

final readonly class RetryOcrJobDTO
{
    public function __construct(
        public ?string $provider = null,
        public ?int    $priority = null,
        public ?string $notes = null,
    ) {}
}