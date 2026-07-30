<?php

namespace App\Domain\AccessControl\DTOs;

readonly class RoleDataDto
{
    public function __construct(
        public int $roleId,
        public array $permissions
    ) {}

    public static function fromRequest(int $roleId, array $requestData): self
    {
        return new self(
            roleId: $roleId,
            permissions: $requestData['permissions'] ?? []
        );
    }
}