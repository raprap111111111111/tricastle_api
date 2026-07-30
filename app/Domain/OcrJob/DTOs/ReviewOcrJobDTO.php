<?php

namespace App\Domain\OcrJob\DTOs;

final readonly class ReviewOcrJobDTO
{
    public function __construct(
        public string  $status,          // completed | requires_review | failed
        public ?string $notes = null,
    ) {}
}