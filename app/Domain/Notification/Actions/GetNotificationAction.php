<?php
// app/Domain/Notification/Actions/GetNotificationAction.php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;
use App\Models\Notification;

class GetNotificationAction
{
    public function __construct(
        private readonly NotificationRepository $repository
    ) {}

    public function execute(string $id): Notification
    {
        return $this->repository->findOrFail($id);
    }
}