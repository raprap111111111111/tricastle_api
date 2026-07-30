<?php
// app/Domain/SocialAccount/DTOs/CreateSocialAccountDTO.php

namespace App\Domain\SocialAccount\DTOs;

final readonly class CreateSocialAccountDTO
{
    public function __construct(
        public int     $userId,
        public string  $provider,
        public string  $providerId,
        public ?string $avatar           = null,
        public ?string $token            = null,
        public ?string $refreshToken     = null,
        public ?string $tokenExpiresAt   = null,
    ) {}
}