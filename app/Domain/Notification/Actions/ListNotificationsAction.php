<?php
// app/Domain/Notification/Actions/ListNotificationsAction.php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;

class ListNotificationsAction
{
    public function __construct(
        private readonly NotificationRepository $repository
    ) {}

    public function execute(array $params, string $resource): array
    {
        $paginated    = $this->repository->paginate($params, $resource);
        $unreadCount  = $this->repository->countUnread();

        return [
            'notifications' => $paginated,
            'unread_count'  => $unreadCount,
        ];
    }
}