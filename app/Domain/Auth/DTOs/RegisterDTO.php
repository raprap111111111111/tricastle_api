<?php

namespace App\Domain\Auth\DTOs;

final readonly class RegisterDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public ?string $middleName = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $role = 'staff',
    ) {}
}
