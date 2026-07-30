<?php

namespace App\Domain\VerificationMismatch\Actions;

use App\Domain\VerificationMismatch\DTOs\ResolveVerificationMismatchDTO;
use App\Domain\VerificationMismatch\Repositories\VerificationMismatchRepository;
use App\Models\VerificationMismatch;

class ResolveVerificationMismatchAction
{
    public function __construct(
        private readonly VerificationMismatchRepository $repository
    ) {}

    public function execute(VerificationMismatch $mismatch, ResolveVerificationMismatchDTO $dto): VerificationMismatch
    {
        return $this->repository->update($mismatch->id, [
            'status'           => $dto->status,
            'resolved_by'      => $dto->resolvedBy,
            'resolved_at'      => now(),
            'resolution_notes' => $dto->resolutionNotes,
        ]);
    }
}