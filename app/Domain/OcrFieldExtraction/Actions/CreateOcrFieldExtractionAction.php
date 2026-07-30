<?php

namespace App\Domain\OcrFieldExtraction\Actions;

use App\Domain\OcrFieldExtraction\DTOs\CreateOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\Repositories\OcrFieldExtractionRepository;
use App\Models\OcrFieldExtraction;

class CreateOcrFieldExtractionAction
{
    public function __construct(
        private readonly OcrFieldExtractionRepository $repository
    ) {}

    public function execute(CreateOcrFieldExtractionDTO $dto): OcrFieldExtraction
    {
        return $this->repository->create([
            'ocr_job_id'            => $dto->ocrJobId,
            'applicant_document_id' => $dto->applicantDocumentId,
            'field_name'            => $dto->fieldName,
            'field_label'           => $dto->fieldLabel,
            'field_type'            => $dto->fieldType,
            'field_category'        => $dto->fieldCategory,
            'is_required'           => $dto->isRequired,
            'is_primary_field'      => $dto->isPrimaryField,
            'sort_order'            => $dto->sortOrder,
            'extracted_value'       => $dto->extractedValue,
            'normalized_value'      => $dto->normalizedValue,
            'confidence_score'      => $dto->confidenceScore,
            'confidence_level'      => $dto->confidenceLevel,
            'character_confidence'  => $dto->characterConfidence,
            'word_confidence'       => $dto->wordConfidence,
            'character_count'       => $dto->characterCount,
            'word_count'            => $dto->wordCount,
            'bounding_box'          => $dto->boundingBox,
            'page_number'           => $dto->pageNumber,
            'x_coordinate'          => $dto->xCoordinate,
            'y_coordinate'          => $dto->yCoordinate,
            'width'                 => $dto->width,
            'height'                => $dto->height,
            'rotation_angle'        => $dto->rotationAngle,
            'status'                => $dto->status,
            'source'                => $dto->source,
            'notes'                 => $dto->notes,
            'metadata'              => $dto->metadata,
            'original_ocr_value'    => $dto->extractedValue, // Preserve the original
        ]);
    }
}