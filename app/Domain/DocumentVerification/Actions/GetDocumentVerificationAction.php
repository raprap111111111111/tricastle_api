<?php

namespace App\Domain\DocumentVerification\Actions;

use App\Domain\DocumentVerification\Repositories\DocumentVerificationRepository;
use App\Models\DocumentVerification;

class GetDocumentVerificationAction
{
    public function __construct(
        private readonly DocumentVerificationRepository $repository
    ) {}

    public function execute(int $id): DocumentVerification
    {
        return $this->repository->findOrFail($id);
    }
}