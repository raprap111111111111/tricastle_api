<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\DTOs\RejectCorrectionRequestDTO;
use App\Domain\CorrectionRequest\Notifications\CorrectionRejectedNotification;
use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;

class RejectCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CorrectionRequest $correctionRequest, RejectCorrectionRequestDTO $dto): CorrectionRequest
    {
        // ─── Update status to rejected ─────────────────────────
        $updated = $this->repository->update($correctionRequest->id, [
            'status'        => 'rejected',
            'justification' => $dto->reason,
        ]);

        // ─── Notify requester request was rejected ─────────────
        if ($updated->requester) {
            $updated->requester->notify(new CorrectionRejectedNotification($updated));
        }

        return $updated;
    }
}