<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\DTOs\CreateDocumentVerificationDTO;
use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class CreateDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(CreateDocumentVerificationDTO $dto): DocumentVerification
    {
        return $this->repository->create([
            'applicant_document_id' => $dto->applicantDocumentId,
            'verified_by'           => $dto->verifiedBy,
            'status'                => 'pending',
            'verification_data'     => $dto->verificationData,
            'source_data'           => $dto->sourceData,
            'notes'                 => $dto->notes,
            'match_percentage'      => 0,
            'total_fields'          => 0,
            'matched_fields'        => 0,
            'mismatched_fields'     => 0,
            'missing_fields'        => 0,
        ]);
    }
}