<?php
namespace App\Domain\Portal\DTOs;

readonly class RegisterPortalDTO {
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public string $email,
        public string $password
    ) {}
}
