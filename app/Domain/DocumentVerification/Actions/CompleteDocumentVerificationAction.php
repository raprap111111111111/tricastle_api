<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\DTOs\CompleteDocumentVerificationDTO;
use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class CompleteDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(DocumentVerification $verification, CompleteDocumentVerificationDTO $dto): DocumentVerification
    {
        $matchPercentage = $dto->totalFields > 0
            ? round(($dto->matchedFields / $dto->totalFields) * 100, 2)
            : 0;

        $timeSpent = $verification->started_at
            ? now()->diffInSeconds($verification->started_at)
            : null;

        return $this->repository->update($verification->id, [
            'status'              => 'completed',
            'verified_by'         => $dto->verifiedBy,
            'total_fields'        => $dto->totalFields,
            'matched_fields'      => $dto->matchedFields,
            'mismatched_fields'   => $dto->mismatchedFields,
            'missing_fields'      => $dto->missingFields,
            'match_percentage'    => $matchPercentage,
            'verification_data'   => $dto->verificationData,
            'source_data'         => $dto->sourceData,
            'notes'               => $dto->notes,
            'completed_at'        => now(),
            'time_spent_seconds'  => $timeSpent,
        ]);
    }
}