<?php

namespace App\Domain\Portal\DTOs;

readonly class VerifyClaimDTO
{
    public string $firstName;
    public string $lastName;
    public string $dateOfBirth;
    public ?string $middleName;

    public function __construct(
        string $firstName,
        string $lastName,
        string $dateOfBirth,
        ?string $middleName = null,
        ?string $email = null,
        ?string $password = null
    ) {
        $this->firstName   = trim($firstName);
        $this->lastName    = trim($lastName);
        $this->dateOfBirth = trim($dateOfBirth);
        $this->middleName  = $middleName ? trim($middleName) : null;
    }
}