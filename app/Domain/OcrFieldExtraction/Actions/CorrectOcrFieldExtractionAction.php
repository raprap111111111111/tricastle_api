<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\DTOs\CorrectOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class CorrectOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(OcrFieldExtraction $extraction, CorrectOcrFieldExtractionDTO $dto): OcrFieldExtraction
    {
        $unrejectableStatuses = ['accepted', 'rejected'];

        if (in_array($extraction->status, $unrejectableStatuses)) {
            throw ValidationException::withMessages([
                'status' => "Field with status [{$extraction->status}] cannot be corrected.",
            ]);
        }

        return $this->repository->update($extraction->id, [
            'final_value'           => $dto->correctedValue,
            'display_value'         => $dto->correctedValue,
            'status'                => 'manually_corrected',
            'was_manually_corrected' => true,
            'correction_reason'     => $dto->correctionReason,
            'correction_count'      => $extraction->correction_count + 1,
            'corrected_by'          => Auth::id(),
            'corrected_at'          => now(),
            'notes'                 => $dto->notes ?? $extraction->notes,
        ]);
    }
}