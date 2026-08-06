<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;

class MarkAllAsReadAction
{
    public function __construct(
        private readonly NotificationRepository $repository,
    ) {}

    public function execute(): int
    {
        return $this->repository->markAllAsRead(auth()->id());
    }
}