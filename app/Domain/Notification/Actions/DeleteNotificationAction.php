<?php
// app/Domain/Notification/Actions/DeleteNotificationAction.php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;
use App\Models\Notification;

class DeleteNotificationAction
{
    public function __construct(
        private readonly NotificationRepository $repository
    ) {}

    public function execute(Notification $notification): void
    {
        $this->repository->delete($notification->id);
    }
}