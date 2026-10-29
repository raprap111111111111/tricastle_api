<?php
namespace App\Domain\Portal\DTOs;

readonly class VerifyClaimDTO {
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth
    ) {}
}
