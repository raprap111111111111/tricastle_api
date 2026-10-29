<?php

namespace App\Domain\Portal\DTOs;

readonly class VerifyClaimDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public ?string $middleName = null,
        public ?string $email = null,
        public ?string $password = null
    ) {}
}