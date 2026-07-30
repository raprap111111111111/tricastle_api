<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\DTOs\RejectOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;
use Illuminate\Validation\ValidationException;

class RejectOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(OcrFieldExtraction $extraction, RejectOcrFieldExtractionDTO $dto): OcrFieldExtraction
    {
        if ($extraction->status === 'accepted') {
            throw ValidationException::withMessages([
                'status' => 'An accepted field extraction cannot be rejected.',
            ]);
        }

        return $this->repository->update($extraction->id, [
            'status'      => 'rejected',
            'final_value' => null,
            'notes'       => $dto->notes ?? $extraction->notes,
        ]);
    }
}