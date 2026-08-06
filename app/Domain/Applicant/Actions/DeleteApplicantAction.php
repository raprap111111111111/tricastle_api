<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Applicant;

class DeleteApplicantAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant): void
    {
        // Capture data BEFORE deletion
        $name       = "{$applicant->first_name} {$applicant->last_name}";
        $code       = $applicant->applicant_code;
        $assignedId = $applicant->assigned_staff_id;

        // 🔔 Notify staff BEFORE deletion (need the record)
        $this->notifyWarning(
            permissions: 'applicant.viewAny',
            title:       '🗑️ Applicant Deleted',
            message:     "{$name} ({$code}) has been permanently removed from the system.",
            module:      'applicant',
        );

        // 🔔 Notify assigned staff personally
        if ($assignedId) {
            $this->notifyUser(
                user:     $assignedId,
                title:    '⚠️ Your applicant was deleted',
                message:  "{$name} ({$code}) — assigned to you — has been deleted.",
                module:   'applicant',
                severity: 'warn',
            );
        }

        $this->repository->delete($applicant->id);
    }
}