<?php

namespace App\Domain\Applicant\Actions;

use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Domain\Notification\Traits\HasNotifications;
use App\Models\Applicant;

class TransferApplicantAction
{
    use HasNotifications;   // 🔔

    public function __construct(
        private readonly ApplicantRepository $repository
    ) {}

    public function execute(Applicant $applicant, int $toStaffId, ?string $reason = null): Applicant
    {
        $fromStaffId = $applicant->assigned_staff_id;

        $updated = $this->repository->update($applicant->id, [
            'assigned_staff_id' => $toStaffId,
        ]);

        $name       = "{$applicant->first_name} {$applicant->last_name}";
        $code       = $applicant->applicant_code;
        $reasonText = $reason ? " Reason: {$reason}" : '';

        // 🔔 Notify NEW staff — you got a new applicant
        $this->notifyUser(
            user:      $toStaffId,
            title:     '📥 Applicant Transferred to You',
            message:   "{$name} ({$code}) has been transferred to you.{$reasonText}",
            module:    'applicant',
            actionUrl: "/applicants/{$applicant->id}",
        );

        // 🔔 Notify OLD staff — applicant moved away
        if ($fromStaffId && $fromStaffId !== $toStaffId) {
            $this->notifyUser(
                user:    $fromStaffId,
                title:   '📤 Applicant Transferred',
                message: "{$name} ({$code}) has been transferred to another staff.{$reasonText}",
                module:  'applicant',
            );
        }

        return $updated;
    }
}