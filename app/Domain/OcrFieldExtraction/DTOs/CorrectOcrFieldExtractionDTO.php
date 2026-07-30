<?php

namespace App\Domain\OcrFieldExtraction\DTOs;

final readonly class CorrectOcrFieldExtractionDTO
{
    public function __construct(
        public string  $correctedValue,
        public ?string $correctionReason = null,
        public ?string $notes = null,
    ) {}
}