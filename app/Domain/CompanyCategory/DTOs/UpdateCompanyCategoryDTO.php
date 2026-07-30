<?php

// app/Domain/CompanyCategory/DTOs/UpdateCompanyCategoryDTO.php

namespace App\Domain\CompanyCategory\DTOs;

final readonly class UpdateCompanyCategoryDTO
{
    public function __construct(
        public ?string $name = null,
        public ?string $slug = null,
        public ?string $description = null,
        public ?bool   $isActive = null,
    ) {}
}