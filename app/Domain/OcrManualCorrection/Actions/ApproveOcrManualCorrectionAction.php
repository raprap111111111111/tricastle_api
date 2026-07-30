<?php

namespace App\Domain\OcrManualCorrection\Actions;

use App\Domain\OcrManualCorrection\DTOs\ApproveOcrManualCorrectionDTO;
use App\Domain\OcrManualCorrection\Repositories\OcrManualCorrectionRepository;
use App\Models\OcrManualCorrection;

class ApproveOcrManualCorrectionAction
{
    public function __construct(
        private readonly OcrManualCorrectionRepository $repository
    ) {}

    public function execute(OcrManualCorrection $correction, ApproveOcrManualCorrectionDTO $dto): OcrManualCorrection
    {
        return $this->repository->update($correction->id, [
            'is_verified'              => true,
            'is_disputed'              => false,
            'verified_by'              => $dto->verifiedBy,
            'verified_at'              => now(),
            'verification_notes'       => $dto->verificationNotes,
            'used_for_training'        => $dto->usedForTraining,
            'added_to_pattern_library' => $dto->addedToPatternLibrary,
            'error_pattern_id'         => $dto->errorPatternId,
        ]);
    }
}