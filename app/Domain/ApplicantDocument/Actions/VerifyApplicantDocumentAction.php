<?php

namespace App\Domain\ApplicantDocument\Actions;

use App\Domain\ApplicantDocument\DTOs\VerifyApplicantDocumentDTO;
use App\Domain\ApplicantDocument\Notifications\DocumentVerifiedNotification;
use App\Domain\ApplicantDocument\Repositories\ApplicantDocumentRepository;
use App\Models\ApplicantDocument;

class VerifyApplicantDocumentAction
{
    public function __construct(
        private readonly ApplicantDocumentRepository $repository
    ) {}

    public function execute(ApplicantDocument $document, VerifyApplicantDocumentDTO $dto): ApplicantDocument
    {
        // ─── Update document status ────────────────────────────
        $updated = $this->repository->update($document->id, array_filter([
            'status'           => 'verified',
            'last_verified_at' => now(),
            'last_verified_by' => $dto->verifiedBy,
            'notes'            => $dto->notes,
            'validated_data'   => $dto->validatedData,
            // ─── Clear rejection info on verify ───────────────
            'rejection_reason' => null,
            'rejected_by'      => null,
            'rejected_at'      => null,
        ], fn($value) => $value !== null));

        // ─── Notify uploader document has been verified ────────
        if ($updated->uploader) {
            $updated->uploader->notify(new DocumentVerifiedNotification($updated));
        }

        return $updated;
    }
}