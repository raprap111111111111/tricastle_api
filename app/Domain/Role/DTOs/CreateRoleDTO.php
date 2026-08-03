<?php
// app/Domain/Role/DTOs/CreateRoleDTO.php

namespace App\Domain\Role\DTOs;

final readonly class CreateRoleDTO
{
    public function __construct(
        public string  $name,
        public ?string $description  = null,
        public array   $permissions  = [],
    ) {}
}