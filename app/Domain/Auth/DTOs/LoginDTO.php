<?php

namespace App\Domain\Auth\DTOs;

final readonly class LoginDTO
{
    public function __construct(
        public string $email,
        public string $password,
        public ?string $deviceName = 'api',
        public bool $rememberMe = false,
    ) {}
}
