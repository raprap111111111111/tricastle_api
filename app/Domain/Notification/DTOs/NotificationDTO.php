<?php
// app/Domain/Notification/DTOs/NotificationDTO.php

namespace App\Domain\Notification\DTOs;

final readonly class NotificationDTO
{
    public function __construct(
        public string  $title,
        public string  $message,
        public ?string $action_url   = null,
        public ?string $action_label = null,
        public ?array  $meta         = null,
    ) {}
}