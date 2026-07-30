<?php

namespace App\Domain\ActivityLog\Actions;

use App\Domain\ActivityLog\Repositories\ActivityLogRepository;
use App\Models\ActivityLog;

class GetActivityLogAction
{
    public function __construct(
        private readonly ActivityLogRepository $repository
    ) {}

    public function execute(int $id): ActivityLog
    {
        return $this->repository->findOrFail($id);
    }
}