<?php
// app/Domain/Permission/DTOs/UpdatePermissionDTO.php

namespace App\Domain\Permission\DTOs;

final readonly class UpdatePermissionDTO
{
    public function __construct(
        public ?string $name        = null,
        public ?string $description = null,
        public ?string $module      = null,
    ) {}
}