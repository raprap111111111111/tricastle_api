<?php

namespace App\Domain\ActivityLog\Traits;

use App\Domain\ActivityLog\Actions\LogActivityAction;
use App\Models\ActivityLog;
use Illuminate\Database\Eloquent\Model;

trait LogsActivity
{
    /**
     * Log an activity. Auto-injects HTTP request context.
     *
     * Usage:
     *   $this->logActivity(
     *       action: 'created',
     *       module: 'Applicant',
     *       description: "Created applicant {$applicant->applicant_code}",
     *       subject: $applicant,
     *       userId: $dto->createdBy,
     *   );
     */
    protected function logActivity(
        string  $action,
        string  $module,
        string  $description,
        ?Model  $subject   = null,
        ?int    $userId    = null,
        ?array  $oldValues = null,
        ?array  $newValues = null,
        ?array  $metadata  = null,
    ): ActivityLog {
        return app(LogActivityAction::class)->execute(
            action:      $action,
            module:      $module,
            description: $description,
            userId:      $userId ?? auth()->id(),
            subjectType: $subject ? get_class($subject) : null,
            subjectId:   $subject?->getKey(),
            oldValues:   $oldValues,
            newValues:   $newValues,
            metadata:    $metadata,
            request:     request(),
        );
    }
}