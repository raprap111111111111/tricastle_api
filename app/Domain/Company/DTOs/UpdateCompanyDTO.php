<?php

// app/Domain/Company/DTOs/UpdateCompanyDTO.php

namespace App\Domain\Company\DTOs;

final readonly class UpdateCompanyDTO
{
    public function __construct(
        public ?string $code = null,
        public ?string $name = null,
        public ?string $nameJapanese = null,
        public ?int    $categoryId = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $prefecture = null,
        public ?string $postalCode = null,
        public ?string $country = null,
        public ?string $contactPerson = null,
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
        public ?string $description = null,
        public ?bool   $isActive = null,
    ) {}
}