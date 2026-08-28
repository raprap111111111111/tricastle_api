<?php

namespace App\Domain\Auth\DTOs;

use Illuminate\Http\UploadedFile;

readonly class UpdateProfileDTO
{
    public function __construct(
        public ?string $firstName = null,
        public ?string $middleName = null,
        public ?string $lastName = null,
        public ?string $suffix = null,
        public ?string $email = null,
        public ?string $phone = null,
        public ?string $mobile = null,
        public ?string $dateOfBirth = null,
        public ?string $gender = null,
        public ?string $department = null,
        public ?string $position = null,
        public ?string $address = null,
        public ?string $city = null,
        public ?string $province = null,
        public ?string $country = null,
        public ?string $postalCode = null,
        public ?string $bio = null,
        public ?UploadedFile $avatar = null,
    ) {}
}