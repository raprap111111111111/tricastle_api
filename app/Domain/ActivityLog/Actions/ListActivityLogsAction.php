<?php

namespace App\Domain\ActivityLog\Actions;

use App\Domain\ActivityLog\Repositories\ActivityLogRepository;

class ListActivityLogsAction
{
    public function __construct(
        private readonly ActivityLogRepository $repository
    ) {}

    public function execute(array $params = [], ?string $resource = null): array
    {
        return $this->repository->paginate($params, $resource);
    }
}