<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class StartDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(DocumentVerification $verification, int $verifiedBy): DocumentVerification
    {
        return $this->repository->update($verification->id, [
            'status'     => 'in_progress',
            'verified_by'=> $verifiedBy,
            'started_at' => now(),
        ]);
    }
}