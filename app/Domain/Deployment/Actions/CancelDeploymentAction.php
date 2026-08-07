<?php

namespace App\Domain\Deployment\Actions;

use App\Domain\Deployment\DTOs\CancelDeploymentDTO;
use App\Domain\Notification\Traits\HasNotifications;
use App\Enums\ApplicantBatchStatus;
use App\Models\ApplicantBatch;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class CancelDeploymentAction
{
    use HasNotifications;

    public function execute(ApplicantBatch $applicantBatch, CancelDeploymentDTO $dto): ApplicantBatch
    {
        // ─── Only deployed records can be cancelled ───
        if ($applicantBatch->status !== ApplicantBatchStatus::DEPLOYED) {
            throw new RuntimeException('Only deployed applicants can be cancelled.');
        }

        DB::transaction(function () use ($applicantBatch, $dto) {
            $applicantBatch->update([
                'status'              => ApplicantBatchStatus::ACCEPTED->value,
                'deployed_at'         => null,
                'cancellation_reason' => $dto->cancellationReason,
                'cancelled_at'        => now(),
                'cancelled_by'        => $dto->cancelledBy,
            ]);
        });

        $applicantBatch->refresh()->load([
            'applicant',
            'batch',
            'cancelledBy',
        ]);

        // ─── 🔔 Notify staff ───
        $applicant = $applicantBatch->applicant;
        if ($applicant?->assigned_staff_id) {
            $this->notifyUser(
                user:      $applicant->assigned_staff_id,
                title:     '❌ Deployment Cancelled',
                message:   "Deployment for {$applicant->first_name} {$applicant->last_name} was cancelled. Reason: {$dto->cancellationReason}",
                module:    'deployment',
                actionUrl: "/deployments/{$applicantBatch->id}",
            );
        }

        return $applicantBatch;
    }
}