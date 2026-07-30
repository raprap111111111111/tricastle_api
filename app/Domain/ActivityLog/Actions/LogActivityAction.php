<?php

namespace App\Domain\ActivityLog\Actions;

use App\Domain\ActivityLog\Repositories\ActivityLogRepository;
use App\Models\ActivityLog;
use Illuminate\Http\Request;

class LogActivityAction
{
    public function __construct(
        private readonly ActivityLogRepository $repository
    ) {}

    public function execute(
        string  $action,
        string  $module,
        string  $description,
        ?int    $userId      = null,
        ?string $subjectType = null,
        ?int    $subjectId   = null,
        ?array  $oldValues   = null,
        ?array  $newValues   = null,
        ?array  $metadata    = null,
        ?Request $request    = null,
    ): ActivityLog {
        return $this->repository->log([
            'user_id'      => $userId,
            'action'       => $action,
            'module'       => $module,
            'subject_type' => $subjectType,
            'subject_id'   => $subjectId,
            'description'  => $description,
            'old_values'   => $oldValues,
            'new_values'   => $newValues,
            'metadata'     => $metadata,
            'ip_address'   => $request?->ip(),
            'user_agent'   => $request?->userAgent(),
            'url'          => $request?->fullUrl(),
            'method'       => $request?->method(),
        ]);
    }
}