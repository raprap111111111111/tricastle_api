<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;
use App\Models\Notification;

class MarkAsReadAction
{
    public function __construct(
        private readonly NotificationRepository $repository,
    ) {}

    public function execute(Notification $notification): Notification
    {
        return $this->repository->markAsRead($notification);
    }
}