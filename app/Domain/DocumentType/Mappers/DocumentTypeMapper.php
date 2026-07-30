<?php

namespace App\Domain\DocumentType\Mappers;

use App\Domain\DocumentType\DTOs\CreateDocumentTypeDTO;
use App\Domain\DocumentType\DTOs\UpdateDocumentTypeDTO;
use App\Http\Requests\v1\DocumentType\StoreDocumentTypeRequest;
use App\Http\Requests\v1\DocumentType\UpdateDocumentTypeRequest;

class DocumentTypeMapper
{
    public static function fromCreateRequest(StoreDocumentTypeRequest $request): CreateDocumentTypeDTO
    {
        return new CreateDocumentTypeDTO(
            name:              $request->validated('name'),
            code:              strtoupper($request->validated('code')),
            description:       $request->validated('description'),
            requiredFields:    $request->validated('required_fields'),
            validationRules:   $request->validated('validation_rules'),
            isRequired:        (bool) $request->validated('is_required', true),
            isActive:          (bool) $request->validated('is_active', true),
            validityDays:      $request->validated('validity_days'),
            expiryWarningDays: (int) $request->validated('expiry_warning_days', 30),
            category:          $request->validated('category', 'primary'),
            sortOrder:         (int) $request->validated('sort_order', 0),
        );
    }

    public static function fromUpdateRequest(UpdateDocumentTypeRequest $request): UpdateDocumentTypeDTO
    {
        return new UpdateDocumentTypeDTO(
            name:              $request->validated('name'),
            code:              $request->has('code') ? strtoupper($request->validated('code')) : null,
            description:       $request->validated('description'),
            requiredFields:    $request->validated('required_fields'),
            validationRules:   $request->validated('validation_rules'),
            isRequired:        $request->has('is_required') ? (bool) $request->validated('is_required') : null,
            isActive:          $request->has('is_active') ? (bool) $request->validated('is_active') : null,
            validityDays:      $request->validated('validity_days'),
            expiryWarningDays: $request->has('expiry_warning_days') ? (int) $request->validated('expiry_warning_days') : null,
            category:          $request->validated('category'),
            sortOrder:         $request->has('sort_order') ? (int) $request->validated('sort_order') : null,
        );
    }
}