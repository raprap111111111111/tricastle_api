<?php

namespace App\Domain\OcrJob\DTOs;

final readonly class UpdateOcrJobDTO
{
    public function __construct(
        public ?string $statusMessage = null,
        public ?string $provider = null,
        public ?array  $providerConfig = null,
        public ?int    $priority = null,
        public ?string $notes = null,
        public ?array  $metadata = null,
        public ?int    $maxAttempts = null,
    ) {}
}