<?php

namespace App\Domain\DocumentType\DTOs;

final readonly class CreateDocumentTypeDTO
{
    public function __construct(
        public string  $name,
        public string  $code,
        public ?string $description       = null,
        public ?array  $requiredFields    = null,
        public ?array  $validationRules   = null,
        public bool    $isRequired        = true,
        public bool    $isActive          = true,
        public ?int    $validityDays      = null,
        public int     $expiryWarningDays = 30,
        public string  $category         = 'primary',
        public int     $sortOrder        = 0,
    ) {}
}