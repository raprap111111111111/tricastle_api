<?php

namespace App\Domain\OcrFieldExtraction\DTOs;

final readonly class RejectOcrFieldExtractionDTO
{
    public function __construct(
        public ?string $notes = null,
    ) {}
}