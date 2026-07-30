<?php

namespace App\Domain\OcrManualCorrection\Actions;

use App\Domain\OcrManualCorrection\DTOs\CancelOcrManualCorrectionDTO;
use App\Domain\OcrManualCorrection\Repositories\OcrManualCorrectionRepository;
use App\Models\OcrManualCorrection;

class CancelOcrManualCorrectionAction
{
    public function __construct(
        private readonly OcrManualCorrectionRepository $repository
    ) {}

    public function execute(OcrManualCorrection $correction, CancelOcrManualCorrectionDTO $dto): OcrManualCorrection
    {
        return $this->repository->update($correction->id, [
            'is_disputed'    => true,
            'reviewed_by'    => $dto->reviewedBy,
            'dispute_reason' => $dto->disputeReason,
            'notes'          => $dto->notes,
        ]);
    }
}