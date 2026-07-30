<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\DTOs\UpdateOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;

class UpdateOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(OcrFieldExtraction $extraction, UpdateOcrFieldExtractionDTO $dto): OcrFieldExtraction
    {
        return $this->repository->update($extraction->id, array_filter([
            'normalized_value'      => $dto->normalizedValue,
            'validated_value'       => $dto->validatedValue,
            'display_value'         => $dto->displayValue,
            'confidence_score'      => $dto->confidenceScore,
            'confidence_level'      => $dto->confidenceLevel,
            'passed_validation'     => $dto->passedValidation,
            'has_validation_errors' => $dto->hasValidationErrors,
            'validation_errors'     => $dto->validationErrors,
            'validation_rule_used'  => $dto->validationRuleUsed,
            'validation_details'    => $dto->validationDetails,
            'status'                => $dto->status,
            'notes'                 => $dto->notes,
            'metadata'              => $dto->metadata,
            'sort_order'            => $dto->sortOrder,
        ], fn($value) => $value !== null));
    }
}