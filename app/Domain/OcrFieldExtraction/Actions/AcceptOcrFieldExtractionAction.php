<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\DTOs\AcceptOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;
use Illuminate\Validation\ValidationException;

class AcceptOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(OcrFieldExtraction $extraction, AcceptOcrFieldExtractionDTO $dto): OcrFieldExtraction
    {
        $acceptableStatuses = [
            'extracted',
            'validated',
            'requires_review',
            'manually_corrected',
        ];

        if (!in_array($extraction->status, $acceptableStatuses)) {
            throw ValidationException::withMessages([
                'status' => "Field with status [{$extraction->status}] cannot be accepted.",
            ]);
        }

        return $this->repository->update($extraction->id, [
            'status'      => 'accepted',
            // Lock in the best available value as final
            'final_value' => $extraction->final_value
                ?? $extraction->validated_value
                ?? $extraction->normalized_value
                ?? $extraction->extracted_value,
            'notes'       => $dto->notes ?? $extraction->notes,
        ]);
    }
}