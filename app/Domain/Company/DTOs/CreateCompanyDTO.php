<?php

// app/Domain/Company/DTOs/CreateCompanyDTO.php

namespace App\Domain\Company\DTOs;

final readonly class CreateCompanyDTO
{
    public function __construct(
        public string  $code,
        public string  $name,
        public int     $categoryId,
        public ?string $nameJapanese = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $prefecture = null,
        public ?string $postalCode = null,
        public string  $country = 'Japan',
        public ?string $contactPerson = null,
        public ?string $contactEmail = null,
        public ?string $contactPhone = null,
        public ?string $description = null,
        public bool    $isActive = true,
    ) {}
}