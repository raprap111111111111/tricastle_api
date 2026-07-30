<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class DeleteDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(DocumentVerification $verification): void
    {
        $this->repository->delete($verification->id);
    }
}