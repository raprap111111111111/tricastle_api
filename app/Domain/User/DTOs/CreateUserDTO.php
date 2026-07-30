<?php

namespace App\Domain\User\DTOs;

use Illuminate\Http\UploadedFile;

final readonly class CreateUserDTO
{
    public function __construct(
        public string $firstName,
        public string $lastName,
        public string $email,
        public string $password,
        public ?string $middleName = null,
        public ?string $suffix = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $employeeCode = null,
        public ?string $department = null,
        public ?string $position = null,
        public ?string $role = null,
        public ?UploadedFile $avatar = null,
        public bool $isActive = true,
    ) {}
}
