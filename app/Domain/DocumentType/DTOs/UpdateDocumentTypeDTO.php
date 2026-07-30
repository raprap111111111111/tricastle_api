<?php

namespace App\Domain\DocumentType\DTOs;

final readonly class UpdateDocumentTypeDTO
{
    public function __construct(
        public ?string $name              = null,
        public ?string $code              = null,
        public ?string $description       = null,
        public ?array  $requiredFields    = null,
        public ?array  $validationRules   = null,
        public ?bool   $isRequired        = null,
        public ?bool   $isActive          = null,
        public ?int    $validityDays      = null,
        public ?int    $expiryWarningDays = null,
        public ?string $category         = null,
        public ?int    $sortOrder        = null,
    ) {}
}