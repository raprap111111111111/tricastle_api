<?php
// app/Domain/DocumentExpiryAlert/Mappers/DocumentExpiryAlertMapper.php

namespace App\Domain\DocumentExpiryAlert\Mappers;

use App\Domain\DocumentExpiryAlert\DTOs\CreateDocumentExpiryAlertDTO;
use App\Http\Requests\v1\DocumentExpiryAlert\StoreDocumentExpiryAlertRequest;

class DocumentExpiryAlertMapper
{
    public static function fromCreateRequest(StoreDocumentExpiryAlertRequest $request): CreateDocumentExpiryAlertDTO
    {
        return new CreateDocumentExpiryAlertDTO(
            applicantDocumentId: $request->validated('applicant_document_id'),
            applicantId:         $request->validated('applicant_id'),
            daysUntilExpiry:     $request->validated('days_until_expiry'),
            alertType:           $request->validated('alert_type'),
            expiryDate:          $request->validated('expiry_date'),
        );
    }
}