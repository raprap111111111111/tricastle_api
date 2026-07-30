<?php

namespace App\Domain\VerificationMismatch\Actions;

use App\Domain\VerificationMismatch\Repositories\VerificationMismatchRepository;
use App\Models\VerificationMismatch;

class DeleteVerificationMismatchAction
{
    public function __construct(
        private readonly VerificationMismatchRepository $repository
    ) {}

    public function execute(VerificationMismatch $mismatch): void
    {
        $this->repository->delete($mismatch->id);
    }
}