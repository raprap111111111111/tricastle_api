<?php
// app/Domain/Notification/Mappers/NotificationMapper.php

namespace App\Domain\Notification\Mappers;

use App\Domain\Notification\DTOs\NotificationDTO;
use App\Models\Notification;

class NotificationMapper
{
    public static function fromModel(Notification $notification): NotificationDTO
    {
        $data = $notification->data ?? [];

        return new NotificationDTO(
            title:        $data['title']        ?? 'Notification',
            message:      $data['message']      ?? '',
            action_url:   $data['action_url']   ?? null,
            action_label: $data['action_label'] ?? null,
            meta:         $data['meta']         ?? null,
        );
    }
}