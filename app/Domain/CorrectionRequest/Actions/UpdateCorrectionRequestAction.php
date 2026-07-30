<?php

namespace App\Domain\CorrectionRequest\Actions;

use App\Domain\CorrectionRequest\DTOs\UpdateCorrectionRequestDTO;
use App\Domain\CorrectionRequest\Repositories\CorrectionRequestRepository;
use App\Models\CorrectionRequest;

class UpdateCorrectionRequestAction
{
    public function __construct(
        private readonly CorrectionRequestRepository $repository
    ) {}

    public function execute(CorrectionRequest $correctionRequest, UpdateCorrectionRequestDTO $dto): CorrectionRequest
    {
        return $this->repository->update($correctionRequest->id, array_filter([
            'description'          => $dto->description,
            'severity'             => $dto->severity,
            'fields_to_correct'    => $dto->fieldsToCorrect,
            'correction_data'      => $dto->correctionData,
            'justification'        => $dto->justification,
            'requires_approval'    => $dto->requiresApproval,
            'requires_new_document'=> $dto->requiresNewDocument,
            'due_date'             => $dto->dueDate,
        ], fn ($value) => $value !== null));
    }
}