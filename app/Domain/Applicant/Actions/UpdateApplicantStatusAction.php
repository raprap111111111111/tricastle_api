<?php

namespace App\Domain\Applicant\Actions;

use App\Constants\PubNubChannels;
use App\Domain\Applicant\DTOs\UpdateStatusDTO;
use App\Domain\Applicant\Repositories\ApplicantRepository;
use App\Enums\ApplicantStatus;
use App\Models\Applicant;
use App\Models\Batch;
use App\Services\PubNubService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class UpdateApplicantStatusAction
{
    public function __construct(
        private readonly ApplicantRepository $repository,
        private readonly PubNubService       $pubnub,
    ) {}

    public function execute(Applicant $applicant, UpdateStatusDTO $dto): Applicant
    {
        return DB::transaction(function () use ($applicant, $dto) {
            $oldStatus = $applicant->status?->value;

            $data = [
                'status'      => $dto->status->value,
                'reviewed_by' => $dto->reviewedBy,
            ];

            if ($dto->status === ApplicantStatus::FinalList) {
                $data['final_listed_at']  = now();
                $data['rejected_at']      = null;
                $data['rejection_reason'] = null;
            }

            if ($dto->status === ApplicantStatus::Rejected) {
                $data['rejected_at']      = now();
                $data['rejection_reason'] = $dto->rejectionReason;
                $data['final_listed_at']  = null;
            }

            if (!in_array($dto->status, [ApplicantStatus::FinalList, ApplicantStatus::Rejected])) {
                $data['final_listed_at']  = null;
                $data['rejected_at']      = null;
                $data['rejection_reason'] = null;
            }

            $updated = $this->repository->update($applicant->id, $data);

            // Auto-assign to active batch
            if ($dto->status === ApplicantStatus::FinalList) {
                $this->autoAssignToActiveBatch($updated, $dto->reviewedBy);
            }

            // 📡 Broadcast via PubNub
            $this->pubnub->broadcast(
                [
                    PubNubChannels::APPLICANTS,
                    PubNubChannels::forApplicant($updated->id),
                ],
                [
                    'event' => 'applicant.status_changed',
                    'payload' => [
                        'applicant_id'   => $updated->id,
                        'applicant_code' => $updated->applicant_code,
                        'name'           => "{$updated->first_name} {$updated->last_name}",
                        'old_status'     => $oldStatus,
                        'new_status'     => $updated->status->value,
                        'changed_by'     => $dto->reviewedBy,
                    ],
                ],
            );

            return $updated->fresh(['applicantBatches.batch']);
        });
    }

    private function autoAssignToActiveBatch(Applicant $applicant, ?int $processedBy): void
    {
        $activeBatch = Batch::where('is_active', true)->first();

        if (!$activeBatch) {
            Log::warning('No active batch found', ['applicant_id' => $applicant->id]);
            return;
        }

        if ($applicant->applicantBatches()->where('batch_id', $activeBatch->id)->exists()) {
            return;
        }

        $applicant->applicantBatches()->create([
            'batch_id'     => $activeBatch->id,
            'status'       => 'assigned',
            'assigned_at'  => now(),
            'processed_by' => $processedBy,
        ]);

        // 📡 Notify batch subscribers
        $this->pubnub->publish(
            PubNubChannels::forBatch($activeBatch->id),
            [
                'event' => 'batch.applicant_assigned',
                'payload' => [
                    'batch_id'       => $activeBatch->id,
                    'batch_name'     => $activeBatch->name,
                    'applicant_id'   => $applicant->id,
                    'applicant_code' => $applicant->applicant_code,
                    'name'           => "{$applicant->first_name} {$applicant->last_name}",
                ],
            ],
        );
    }
}