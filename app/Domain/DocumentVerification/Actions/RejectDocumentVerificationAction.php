<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\DTOs\RejectDocumentVerificationDTO;
use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class RejectDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(DocumentVerification $verification, RejectDocumentVerificationDTO $dto): DocumentVerification
    {
        return $this->repository->update($verification->id, [
            'status'           => 'rejected',
            'rejection_reason' => $dto->rejectionReason,
            'reviewed_by'      => $dto->reviewedBy,
            'notes'            => $dto->notes,
        ]);
    }
}