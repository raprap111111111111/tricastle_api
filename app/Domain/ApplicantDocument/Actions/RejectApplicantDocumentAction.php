<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\RejectApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Models\ApplicantDocument;

class RejectApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document, RejectApplicantDocumentDTO $dto): ApplicantDocument
    {
        return $this->repository->update($document->id, [
            'status'           => 'rejected',
            'rejection_reason' => $dto->rejectionReason,
            'rejected_by'      => $dto->rejectedBy,
            'rejected_at'      => now(),
            'notes'            => $dto->notes,
        ]);
    }
}