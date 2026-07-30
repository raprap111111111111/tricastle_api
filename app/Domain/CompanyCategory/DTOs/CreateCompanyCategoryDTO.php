<?php

// app/Domain/CompanyCategory/DTOs/CreateCompanyCategoryDTO.php

namespace App\Domain\CompanyCategory\DTOs;

final readonly class CreateCompanyCategoryDTO
{
    public function __construct(
        public string  $name,
        public ?string $slug = null,
        public ?string $description = null,
        public bool    $isActive = true,
    ) {}
}