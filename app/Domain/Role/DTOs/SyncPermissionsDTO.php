<?php
// app/Domain/Role/DTOs/SyncPermissionsDTO.php

namespace App\Domain\Role\DTOs;

final readonly class SyncPermissionsDTO
{
    public function __construct(
        public array $permissions,
    ) {}
}