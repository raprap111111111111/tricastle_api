<?php
// app/Domain/LoginHistory/DTOs/CreateLoginHistoryDTO.php

namespace App\Domain\LoginHistory\DTOs;

final readonly class CreateLoginHistoryDTO
{
    public function __construct(
        public int     $userId,
        public ?string $ipAddress     = null,
        public ?string $userAgent     = null,
        public ?string $deviceType    = null,
        public ?string $browser       = null,
        public ?string $platform      = null,
        public ?string $location      = null,
        public string  $status        = 'success',
        public ?string $failureReason = null,
        public string  $loginMethod   = 'email',
        public ?string $loggedInAt    = null,
    ) {}
}