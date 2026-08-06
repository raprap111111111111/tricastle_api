<?php

namespace App\Domain\Notification\Actions;

use App\Domain\Notification\Repositories\NotificationRepository;

class ListNotificationsAction
{
    public function __construct(
        private readonly NotificationRepository $repository,
    ) {}

    public function execute(array $filters, string $resourceClass = null)
    {
        $filters['user_id'] = auth()->id();
        return $this->repository->list($filters);
    }
}