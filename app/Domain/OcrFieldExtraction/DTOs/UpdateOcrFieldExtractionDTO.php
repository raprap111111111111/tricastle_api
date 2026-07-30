<?php

namespace App\Domain\OcrFieldExtraction\DTOs;

final readonly class UpdateOcrFieldExtractionDTO
{
    public function __construct(
        public ?string $normalizedValue = null,
        public ?string $validatedValue = null,
        public ?string $displayValue = null,
        public ?float  $confidenceScore = null,
        public ?string $confidenceLevel = null,
        public ?bool   $passedValidation = null,
        public ?bool   $hasValidationErrors = null,
        public ?string $validationErrors = null,
        public ?string $validationRuleUsed = null,
        public ?array  $validationDetails = null,
        public ?string $status = null,
        public ?string $notes = null,
        public ?array  $metadata = null,
        public ?int    $sortOrder = null,
    ) {}
}