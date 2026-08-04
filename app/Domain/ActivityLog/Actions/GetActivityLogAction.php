<?php

namespace App\Domain\ActivityLog\Actions;

use App\Domain\ActivityLog\Repositories\ActivityLogRepository;
use Spatie\Activitylog\Models\Activity;

class GetActivityLogAction
{
    public function __construct(
        private readonly ActivityLogRepository $repository
    ) {}

    public function execute(int $id): ?Activity
    {
        return $this->repository->find($id, ['causer', 'subject']);
    }
}