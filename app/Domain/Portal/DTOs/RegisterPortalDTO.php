<?php

namespace App\Domain\Portal\DTOs;

readonly class RegisterPortalDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $dateOfBirth,
        public string $email,
        public string $password,
        public ?string $middleName = null
    ) {}

    public static function fromRequest(\App\Http\Requests\v1\Portal\RegisterPortalRequest $request): self
    {
        return new self(
            firstName: $request->validated('first_name'),
            middleName: $request->validated('middle_name'),
            lastName: $request->validated('last_name'),
            dateOfBirth: $request->validated('date_of_birth'),
            email: $request->validated('email'),
            password: $request->validated('password')
        );
    }
}