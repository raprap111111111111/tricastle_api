<?php

// app/Domain/Setting/DTOs/UpdateSettingDTO.php

namespace App\Domain\Setting\DTOs;

final readonly class UpdateSettingDTO
{
    public function __construct(
        public ?string $key = null,
        public ?string $value = null,
        public ?string $type = null,
        public ?string $group = null,
        public ?string $description = null,
        public ?bool   $isPublic = null,
    ) {}
}