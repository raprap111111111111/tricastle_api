<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\Notifications\CorrectionCancelledNotification;
use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;
use App\Models\User;

class CancelCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CorrectionRequest $correctionRequest, int $cancelledBy, ?string $reason = null): CorrectionRequest
    {
        // ─── Update status to cancelled ────────────────────────
        $updated = $this->repository->update($correctionRequest->id, [
            'status'        => 'cancelled',
            'justification' => $reason ?? $correctionRequest->justification,
        ]);

        // ─── Notify users who can manage correction requests ───
        User::permission('correction-request.viewAny')
            ->get()
            ->each(fn(User $user) => $user->notify(
                new CorrectionCancelledNotification($updated)
            ));

        return $updated;
    }
}