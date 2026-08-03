<?php
// app/Domain/Role/DTOs/UpdateRoleDTO.php

namespace App\Domain\Role\DTOs;

final readonly class UpdateRoleDTO
{
    public function __construct(
        public ?string $name         = null,
        public ?string $description  = null,
    ) {}
}