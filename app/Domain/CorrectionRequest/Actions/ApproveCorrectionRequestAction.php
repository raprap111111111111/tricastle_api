<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\DTOs\ApproveCorrectionRequestDTO;
use App\Domain\CorrectionRequest\Notifications\CorrectionApprovedNotification;
use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;

class ApproveCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CorrectionRequest $correctionRequest, ApproveCorrectionRequestDTO $dto): CorrectionRequest
    {
        // ─── Update status to approved ─────────────────────────
        $updated = $this->repository->update($correctionRequest->id, array_filter([
            'status'   => 'approved',
            'due_date' => $dto->dueDate ?? $correctionRequest->due_date,
        ], fn($value) => $value !== null));

        // ─── Notify requester request was approved ─────────────
        if ($updated->requester) {
            $updated->requester->notify(new CorrectionApprovedNotification($updated));
        }

        return $updated;
    }
}