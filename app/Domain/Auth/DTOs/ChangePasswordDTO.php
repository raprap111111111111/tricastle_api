<?php

namespace App\Domain\Auth\DTOs;

final readonly class ChangePasswordDTO
{
    public function __construct(
        public string $currentPassword,
        public string $newPassword,
    ) {}
}
