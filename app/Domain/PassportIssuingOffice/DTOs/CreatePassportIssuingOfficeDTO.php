<?php

namespace App\Domain\PassportIssuingOffice\DTOs;

final readonly class CreatePassportIssuingOfficeDTO
{
    public function __construct(
        public string  $region,
        public string  $name,
        public ?string $code      = null,
        public ?string $address   = null,
        public ?string $city      = null,
        public ?string $province  = null,
        public bool    $isActive  = true,
        public int     $sortOrder = 0,
        public ?string $notes     = null,
    ) {}
}