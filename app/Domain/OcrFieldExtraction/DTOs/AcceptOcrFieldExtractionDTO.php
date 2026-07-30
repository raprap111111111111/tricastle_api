<?php

namespace App\Domain\OcrFieldExtraction\DTOs;

final readonly class AcceptOcrFieldExtractionDTO
{
    public function __construct(
        public ?string $notes = null,
    ) {}
}