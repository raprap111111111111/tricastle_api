<?php

namespace App\Domain\VerificationMismatch\Actions;

use App\Domain\VerificationMismatch\Repositories\VerificationMismatchRepository;
use App\Models\VerificationMismatch;

class GetVerificationMismatchAction
{
    public function __construct(
        private readonly VerificationMismatchRepository $repository
    ) {}

    public function execute(int $id): VerificationMismatch
    {
        return $this->repository->findOrFail($id);
    }
}