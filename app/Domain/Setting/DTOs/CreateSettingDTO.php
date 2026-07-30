<?php

// app/Domain/Setting/DTOs/CreateSettingDTO.php

namespace App\Domain\Setting\DTOs;

final readonly class CreateSettingDTO
{
    public function __construct(
        public string  $key,
        public ?string $value = null,
        public string  $type = 'string',
        public string  $group = 'general',
        public ?string $description = null,
        public bool    $isPublic = false,
    ) {}
}