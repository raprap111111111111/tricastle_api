<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\DTOs\CompleteCorrectionRequestDTO;
use App\Domain\CorrectionRequest\Notifications\CorrectionCompletedNotification;
use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;

class CompleteCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CorrectionRequest $correctionRequest, CompleteCorrectionRequestDTO $dto): CorrectionRequest
    {
        // ─── Update status to completed ────────────────────────
        $updated = $this->repository->update($correctionRequest->id, array_filter([
            'status'          => 'completed',
            'correction_data' => $dto->correctionData,
        ], fn($value) => $value !== null));

        // ─── Notify requester request was completed ────────────
        if ($updated->requester) {
            $updated->requester->notify(new CorrectionCompletedNotification($updated));
        }

        return $updated;
    }
}