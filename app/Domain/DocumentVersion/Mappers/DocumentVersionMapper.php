<?php

namespace App\Domain\DocumentVersion\Mappers;

use App\Domain\DocumentVersion\DTOs\CreateDocumentVersionDTO;
use App\Http\Requests\v1\DocumentVersion\StoreDocumentVersionRequest;

class DocumentVersionMapper
{
    public static function fromCreateRequest(StoreDocumentVersionRequest $request): CreateDocumentVersionDTO
    {
        return new CreateDocumentVersionDTO(
            applicantDocumentId: (int) $request->validated('applicant_document_id'),
            file:                $request->file('file'),
            changeReason:        $request->validated('change_reason'),
            extractedData:       $request->validated('extracted_data'),
            uploadedBy:          $request->user()?->id,
        );
    }
}