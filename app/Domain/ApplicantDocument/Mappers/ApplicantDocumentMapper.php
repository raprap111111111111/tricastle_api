<?php

namespace App\Domain\ApplicantDocument\Mappers;

use App\Domain\ApplicantDocument\DTOs\RejectApplicantDocumentDTO;
use App\Domain\ApplicantDocument\DTOs\UpdateApplicantDocumentDTO;
use App\Domain\ApplicantDocument\DTOs\UploadApplicantDocumentDTO;
use App\Domain\ApplicantDocument\DTOs\VerifyApplicantDocumentDTO;
use App\Http\Requests\v1\ApplicantDocument\RejectApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UpdateApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\UploadApplicantDocumentRequest;
use App\Http\Requests\v1\ApplicantDocument\VerifyApplicantDocumentRequest;

class ApplicantDocumentMapper
{
    public static function fromUploadRequest(UploadApplicantDocumentRequest $request): UploadApplicantDocumentDTO
    {
        return new UploadApplicantDocumentDTO(
            applicantId:    (int) $request->validated('applicant_id'),
            documentTypeId: (int) $request->validated('document_type_id'),
            file:           $request->file('file'),
            documentDate:   $request->validated('document_date'),
            expiryDate:     $request->validated('expiry_date'),
            priority:       $request->validated('priority', 'normal'),
            notes:          $request->validated('notes'),
            metadata:       $request->validated('metadata'),
            uploadedBy:     $request->user()?->id,
        );
    }

    public static function fromUpdateRequest(UpdateApplicantDocumentRequest $request): UpdateApplicantDocumentDTO
    {
        return new UpdateApplicantDocumentDTO(
            documentDate:  $request->validated('document_date'),
            expiryDate:    $request->validated('expiry_date'),
            priority:      $request->validated('priority'),
            notes:         $request->validated('notes'),
            metadata:      $request->validated('metadata'),
            validatedData: $request->validated('validated_data'),
        );
    }

    public static function fromVerifyRequest(VerifyApplicantDocumentRequest $request): VerifyApplicantDocumentDTO
    {
        return new VerifyApplicantDocumentDTO(
            verifiedBy:    $request->user()->id,
            notes:         $request->validated('notes'),
            validatedData: $request->validated('validated_data'),
        );
    }

    public static function fromRejectRequest(RejectApplicantDocumentRequest $request): RejectApplicantDocumentDTO
    {
        return new RejectApplicantDocumentDTO(
            rejectionReason: $request->validated('rejection_reason'),
            rejectedBy:      $request->user()->id,
            notes:           $request->validated('notes'),
        );
    }
}