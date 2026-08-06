<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Applicant;

class AssignApplicantAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant, int $staffId): Applicant
    {
        $updated = $this->repository->update($applicant->id, [
            'assigned_staff_id' => $staffId,
        ]);

        // 🔔 Notify newly assigned staff
        $this->notifyUser(
            user:      $staffId,
            title:     '📋 New Applicant Assigned',
            message:   "You've been assigned to {$applicant->first_name} {$applicant->last_name} ({$applicant->applicant_code}).",
            module:    'applicant',
            actionUrl: "/applicants/{$applicant->id}",
        );

        return $updated;
    }
}