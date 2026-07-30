<?php

namespace App\Domain\DocumentVerification\Mappers;

use App\Domain\DocumentVerification\DTOs\CompleteDocumentVerificationDTO;
use App\Domain\DocumentVerification\DTOs\CreateDocumentVerificationDTO;
use App\Domain\DocumentVerification\DTOs\RejectDocumentVerificationDTO;
use App\Domain\DocumentVerification\DTOs\UpdateDocumentVerificationDTO;
use App\Http\Requests\v1\DocumentVerification\CompleteDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\RejectDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\StoreDocumentVerificationRequest;
use App\Http\Requests\v1\DocumentVerification\UpdateDocumentVerificationRequest;

class DocumentVerificationMapper
{
    public static function fromCreateRequest(StoreDocumentVerificationRequest $request): CreateDocumentVerificationDTO
    {
        return new CreateDocumentVerificationDTO(
            applicantDocumentId: (int) $request->validated('applicant_document_id'),
            verifiedBy:          $request->user()->id,
            verificationData:    $request->validated('verification_data'),
            sourceData:          $request->validated('source_data'),
            notes:               $request->validated('notes'),
        );
    }

    public static function fromUpdateRequest(UpdateDocumentVerificationRequest $request): UpdateDocumentVerificationDTO
    {
        return new UpdateDocumentVerificationDTO(
            verificationData: $request->validated('verification_data'),
            sourceData:       $request->validated('source_data'),
            totalFields:      $request->validated('total_fields'),
            matchedFields:    $request->validated('matched_fields'),
            mismatchedFields: $request->validated('mismatched_fields'),
            missingFields:    $request->validated('missing_fields'),
            notes:            $request->validated('notes'),
            reviewedBy:       $request->validated('reviewed_by'),
        );
    }

    public static function fromCompleteRequest(CompleteDocumentVerificationRequest $request): CompleteDocumentVerificationDTO
    {
        return new CompleteDocumentVerificationDTO(
            verifiedBy:       $request->user()->id,
            totalFields:      (int) $request->validated('total_fields'),
            matchedFields:    (int) $request->validated('matched_fields'),
            mismatchedFields: (int) $request->validated('mismatched_fields'),
            missingFields:    (int) $request->validated('missing_fields'),
            verificationData: $request->validated('verification_data'),
            sourceData:       $request->validated('source_data'),
            notes:            $request->validated('notes'),
        );
    }

    public static function fromRejectRequest(RejectDocumentVerificationRequest $request): RejectDocumentVerificationDTO
    {
        return new RejectDocumentVerificationDTO(
            rejectionReason: $request->validated('rejection_reason'),
            reviewedBy:      $request->user()->id,
            notes:           $request->validated('notes'),
        );
    }
}