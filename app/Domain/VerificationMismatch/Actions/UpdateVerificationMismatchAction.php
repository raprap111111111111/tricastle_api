<?php

namespace App\Domain\VerificationMismatch\Actions;

use App\Domain\VerificationMismatch\DTOs\UpdateVerificationMismatchDTO;
use App\Domain\VerificationMismatch\Repositories\VerificationMismatchRepository;
use App\Models\VerificationMismatch;

class UpdateVerificationMismatchAction
{
    public function __construct(
        private readonly VerificationMismatchRepository $repository
    ) {}

    public function execute(VerificationMismatch $mismatch, UpdateVerificationMismatchDTO $dto): VerificationMismatch
    {
        return $this->repository->update($mismatch->id, array_filter([
            'field_name'       => $dto->fieldName,
            'field_label'      => $dto->fieldLabel,
            'source_value'     => $dto->sourceValue,
            'entered_value'    => $dto->enteredValue,
            'severity'         => $dto->severity,
            'mismatch_type'    => $dto->mismatchType,
            'status'           => $dto->status,
            'resolution_notes' => $dto->resolutionNotes,
        ], fn ($value) => $value !== null));
    }
}