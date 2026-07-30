<?php

namespace App\Domain\OcrFieldExtraction\Mappers;

use App\Domain\OcrFieldExtraction\DTOs\AcceptOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\DTOs\CorrectOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\DTOs\CreateOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\DTOs\RejectOcrFieldExtractionDTO;
use App\Domain\OcrFieldExtraction\DTOs\UpdateOcrFieldExtractionDTO;
use App\Http\Requests\v1\OcrFieldExtraction\AcceptOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\CorrectOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\RejectOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\StoreOcrFieldExtractionRequest;
use App\Http\Requests\v1\OcrFieldExtraction\UpdateOcrFieldExtractionRequest;

class OcrFieldExtractionMapper
{
    public static function fromCreateRequest(
        StoreOcrFieldExtractionRequest $request
    ): CreateOcrFieldExtractionDTO {
        return new CreateOcrFieldExtractionDTO(
            ocrJobId:            $request->validated('ocr_job_id'),
            applicantDocumentId: $request->validated('applicant_document_id'),
            fieldName:           $request->validated('field_name'),
            fieldLabel:          $request->validated('field_label'),
            fieldType:           $request->validated('field_type'),
            fieldCategory:       $request->validated('field_category'),
            isRequired:          $request->validated('is_required', false),
            isPrimaryField:      $request->validated('is_primary_field', false),
            sortOrder:           $request->validated('sort_order', 0),
            extractedValue:      $request->validated('extracted_value'),
            normalizedValue:     $request->validated('normalized_value'),
            confidenceScore:     $request->validated('confidence_score', 0),
            confidenceLevel:     $request->validated('confidence_level', 'unknown'),
            characterConfidence: $request->validated('character_confidence'),
            wordConfidence:      $request->validated('word_confidence'),
            characterCount:      $request->validated('character_count'),
            wordCount:           $request->validated('word_count'),
            boundingBox:         $request->validated('bounding_box'),
            pageNumber:          $request->validated('page_number', 1),
            xCoordinate:         $request->validated('x_coordinate'),
            yCoordinate:         $request->validated('y_coordinate'),
            width:               $request->validated('width'),
            height:              $request->validated('height'),
            rotationAngle:       $request->validated('rotation_angle'),
            status:              $request->validated('status', 'extracted'),
            source:              $request->validated('source', 'ocr'),
            notes:               $request->validated('notes'),
            metadata:            $request->validated('metadata'),
        );
    }

    public static function fromUpdateRequest(
        UpdateOcrFieldExtractionRequest $request
    ): UpdateOcrFieldExtractionDTO {
        return new UpdateOcrFieldExtractionDTO(
            normalizedValue:     $request->validated('normalized_value'),
            validatedValue:      $request->validated('validated_value'),
            displayValue:        $request->validated('display_value'),
            confidenceScore:     $request->validated('confidence_score'),
            confidenceLevel:     $request->validated('confidence_level'),
            passedValidation:    $request->validated('passed_validation'),
            hasValidationErrors: $request->validated('has_validation_errors'),
            validationErrors:    $request->validated('validation_errors'),
            validationRuleUsed:  $request->validated('validation_rule_used'),
            validationDetails:   $request->validated('validation_details'),
            status:              $request->validated('status'),
            notes:               $request->validated('notes'),
            metadata:            $request->validated('metadata'),
            sortOrder:           $request->validated('sort_order'),
        );
    }

    public static function fromCorrectRequest(
        CorrectOcrFieldExtractionRequest $request
    ): CorrectOcrFieldExtractionDTO {
        return new CorrectOcrFieldExtractionDTO(
            correctedValue:   $request->validated('corrected_value'),
            correctionReason: $request->validated('correction_reason'),
            notes:            $request->validated('notes'),
        );
    }

    public static function fromAcceptRequest(
        AcceptOcrFieldExtractionRequest $request
    ): AcceptOcrFieldExtractionDTO {
        return new AcceptOcrFieldExtractionDTO(
            notes: $request->validated('notes'),
        );
    }

    public static function fromRejectRequest(
        RejectOcrFieldExtractionRequest $request
    ): RejectOcrFieldExtractionDTO {
        return new RejectOcrFieldExtractionDTO(
            notes: $request->validated('notes'),
        );
    }
}