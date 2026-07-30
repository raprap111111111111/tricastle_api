<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\DTOs\UpdateDocumentVerificationDTO;
use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class UpdateDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(DocumentVerification $verification, UpdateDocumentVerificationDTO $dto): DocumentVerification
    {
        $data = array_filter([
            'verification_data' => $dto->verificationData,
            'source_data'       => $dto->sourceData,
            'total_fields'      => $dto->totalFields,
            'matched_fields'    => $dto->matchedFields,
            'mismatched_fields' => $dto->mismatchedFields,
            'missing_fields'    => $dto->missingFields,
            'notes'             => $dto->notes,
            'reviewed_by'       => $dto->reviewedBy,
        ], fn ($value) => $value !== null);

        // Recalculate match percentage if fields updated
        if (isset($data['total_fields']) && $data['total_fields'] > 0) {
            $matched                  = $data['matched_fields'] ?? $verification->matched_fields;
            $total                    = $data['total_fields'];
            $data['match_percentage'] = round(($matched / $total) * 100, 2);
        }

        return $this->repository->update($verification->id, $data);
    }
}