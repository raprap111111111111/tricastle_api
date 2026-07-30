<?php

namespace App\Domain\CorrectionRequest\Mappers;

use App\Domain\CorrectionRequest\DTOs\ApproveCorrectionRequestDTO;
use App\Domain\CorrectionRequest\DTOs\CompleteCorrectionRequestDTO;
use App\Domain\CorrectionRequest\DTOs\CreateCorrectionRequestDTO;
use App\Domain\CorrectionRequest\DTOs\RejectCorrectionRequestDTO;
use App\Domain\CorrectionRequest\DTOs\UpdateCorrectionRequestDTO;
use App\Http\Requests\v1\CorrectionRequest\ApproveCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\CompleteCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\RejectCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\StoreCorrectionRequestRequest;
use App\Http\Requests\v1\CorrectionRequest\UpdateCorrectionRequestRequest;

class CorrectionRequestMapper
{
    public static function fromCreateRequest(StoreCorrectionRequestRequest $request): CreateCorrectionRequestDTO
    {
        return new CreateCorrectionRequestDTO(
            documentVerificationId: (int) $request->validated('document_verification_id'),
            applicantDocumentId:    (int) $request->validated('applicant_document_id'),
            requestedBy:            $request->user()->id,
            description:            $request->validated('description'),
            severity:               $request->validated('severity', 'low'),
            fieldsToCorrect:        $request->validated('fields_to_correct'),
            correctionData:         $request->validated('correction_data'),
            justification:          $request->validated('justification'),
            requiresApproval:       (bool) $request->validated('requires_approval', false),
            requiresNewDocument:    (bool) $request->validated('requires_new_document', false),
            dueDate:                $request->validated('due_date'),
        );
    }

    public static function fromUpdateRequest(UpdateCorrectionRequestRequest $request): UpdateCorrectionRequestDTO
    {
        return new UpdateCorrectionRequestDTO(
            description:         $request->validated('description'),
            severity:            $request->validated('severity'),
            fieldsToCorrect:     $request->validated('fields_to_correct'),
            correctionData:      $request->validated('correction_data'),
            justification:       $request->validated('justification'),
            requiresApproval:    $request->has('requires_approval')
                ? (bool) $request->validated('requires_approval')
                : null,
            requiresNewDocument: $request->has('requires_new_document')
                ? (bool) $request->validated('requires_new_document')
                : null,
            dueDate:             $request->validated('due_date'),
        );
    }

    public static function fromApproveRequest(ApproveCorrectionRequestRequest $request): ApproveCorrectionRequestDTO
    {
        return new ApproveCorrectionRequestDTO(
            approvedBy: $request->user()->id,
            notes:      $request->validated('notes'),
            dueDate:    $request->validated('due_date'),
        );
    }

    public static function fromRejectRequest(RejectCorrectionRequestRequest $request): RejectCorrectionRequestDTO
    {
        return new RejectCorrectionRequestDTO(
            rejectedBy: $request->user()->id,
            reason:     $request->validated('reason'),
            notes:      $request->validated('notes'),
        );
    }

    public static function fromCompleteRequest(CompleteCorrectionRequestRequest $request): CompleteCorrectionRequestDTO
    {
        return new CompleteCorrectionRequestDTO(
            completedBy:    $request->user()->id,
            correctionData: $request->validated('correction_data'),
            notes:          $request->validated('notes'),
        );
    }
}