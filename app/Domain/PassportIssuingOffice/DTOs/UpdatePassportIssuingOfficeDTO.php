<?php

namespace App\Domain\PassportIssuingOffice\DTOs;

final readonly class UpdatePassportIssuingOfficeDTO
{
    public function __construct(
        public ?string $region    = null,
        public ?string $name      = null,
        public ?string $code      = null,
        public ?string $address   = null,
        public ?string $city      = null,
        public ?string $province  = null,
        public ?bool   $isActive  = null,
        public ?int    $sortOrder = null,
        public ?string $notes     = null,
    ) {}
}