<?php

namespace App\Domain\VerificationMismatch\Mappers;

use App\Domain\VerificationMismatch\DTOs\CreateVerificationMismatchDTO;
use App\Domain\VerificationMismatch\DTOs\ResolveVerificationMismatchDTO;
use App\Domain\VerificationMismatch\DTOs\UpdateVerificationMismatchDTO;
use App\Http\Requests\v1\VerificationMismatch\EscalateVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\ResolveVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\StoreVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\UpdateVerificationMismatchRequest;
use App\Http\Requests\v1\VerificationMismatch\WaiveVerificationMismatchRequest;

class VerificationMismatchMapper
{
    public static function fromCreateRequest(StoreVerificationMismatchRequest $request): CreateVerificationMismatchDTO
    {
        return new CreateVerificationMismatchDTO(
            documentVerificationId: (int) $request->validated('document_verification_id'),
            fieldName:              $request->validated('field_name'),
            fieldLabel:             $request->validated('field_label'),
            sourceValue:            $request->validated('source_value'),
            enteredValue:           $request->validated('entered_value'),
            severity:               $request->validated('severity', 'low'),
            mismatchType:           $request->validated('mismatch_type', 'value_mismatch'),
            status:                 $request->validated('status', 'open'),
        );
    }

    public static function fromUpdateRequest(UpdateVerificationMismatchRequest $request): UpdateVerificationMismatchDTO
    {
        return new UpdateVerificationMismatchDTO(
            fieldName:       $request->validated('field_name'),
            fieldLabel:      $request->validated('field_label'),
            sourceValue:     $request->validated('source_value'),
            enteredValue:    $request->validated('entered_value'),
            severity:        $request->validated('severity'),
            mismatchType:    $request->validated('mismatch_type'),
            status:          $request->validated('status'),
            resolutionNotes: $request->validated('resolution_notes'),
        );
    }

    public static function fromResolveRequest(ResolveVerificationMismatchRequest $request): ResolveVerificationMismatchDTO
    {
        return new ResolveVerificationMismatchDTO(
            status:          'corrected',
            resolvedBy:      $request->user()->id,
            resolutionNotes: $request->validated('resolution_notes'),
        );
    }

    public static function fromWaiveRequest(WaiveVerificationMismatchRequest $request): ResolveVerificationMismatchDTO
    {
        return new ResolveVerificationMismatchDTO(
            status:          'waived',
            resolvedBy:      $request->user()->id,
            resolutionNotes: $request->validated('resolution_notes'),
        );
    }

    public static function fromEscalateRequest(EscalateVerificationMismatchRequest $request): ResolveVerificationMismatchDTO
    {
        return new ResolveVerificationMismatchDTO(
            status:          'escalated',
            resolvedBy:      $request->user()->id,
            resolutionNotes: $request->validated('resolution_notes'),
        );
    }
}