<?php

namespace App\Domain\User\DTOs;

use Illuminate\Http\UploadedFile;

final readonly class UpdateUserDTO
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $lastName = null,
        public ?string $middleName = null,
        public ?string $suffix = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $employeeCode = null,
        public ?string $department = null,
        public ?string $position = null,
        public ?string $role = null,
        public ?string $password = null,
        public ?UploadedFile $avatar = null,
        public ?bool $isActive = null,
    ) {}
}
