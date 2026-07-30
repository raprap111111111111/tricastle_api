<?php

namespace App\Domain\OcrManualCorrection\Actions;

use App\Domain\OcrManualCorrection\DTOs\RejectOcrManualCorrectionDTO;
use App\Domain\OcrManualCorrection\Repositories\OcrManualCorrectionRepository;
use App\Models\OcrManualCorrection;

class RejectOcrManualCorrectionAction
{
    public function __construct(
        private readonly OcrManualCorrectionRepository $repository
    ) {}

    public function execute(OcrManualCorrection $correction, RejectOcrManualCorrectionDTO $dto): OcrManualCorrection
    {
        return $this->repository->update($correction->id, [
            'is_disputed'        => true,
            'is_verified'        => false,
            'reviewed_by'        => $dto->reviewedBy,
            'dispute_reason'     => $dto->disputeReason,
            'verification_notes' => $dto->verificationNotes,
        ]);
    }
}