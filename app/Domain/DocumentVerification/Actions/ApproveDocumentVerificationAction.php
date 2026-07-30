<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class ApproveDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(DocumentVerification $verification, int $reviewedBy, ?string $notes = null): DocumentVerification
    {
        return $this->repository->update($verification->id, [
            'status'      => 'approved',
            'reviewed_by' => $reviewedBy,
            'notes'       => $notes ?? $verification->notes,
        ]);
    }
}