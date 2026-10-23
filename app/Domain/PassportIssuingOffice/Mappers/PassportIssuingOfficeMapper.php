<?php

namespace App\Domain\PassportIssuingOffice\Mappers;

use App\Domain\PassportIssuingOffice\DTOs\CreatePassportIssuingOfficeDTO;
use App\Domain\PassportIssuingOffice\DTOs\UpdatePassportIssuingOfficeDTO;
use App\Http\Requests\v1\PassportIssuingOffice\StorePassportIssuingOfficeRequest;
use App\Http\Requests\v1\PassportIssuingOffice\UpdatePassportIssuingOfficeRequest;

class PassportIssuingOfficeMapper
{
    public static function fromCreateRequest(StorePassportIssuingOfficeRequest $request): CreatePassportIssuingOfficeDTO
    {
        return new CreatePassportIssuingOfficeDTO(
            region:    $request->validated('region'),
            name:      $request->validated('name'),
            code:      $request->validated('code'),
            address:   $request->validated('address'),
            city:      $request->validated('city'),
            province:  $request->validated('province'),
            isActive:  (bool) ($request->validated('is_active') ?? true),
            sortOrder: (int) ($request->validated('sort_order') ?? 0),
            notes:     $request->validated('notes'),
        );
    }

    public static function fromUpdateRequest(UpdatePassportIssuingOfficeRequest $request): UpdatePassportIssuingOfficeDTO
    {
        return new UpdatePassportIssuingOfficeDTO(
            region:    $request->validated('region'),
            name:      $request->validated('name'),
            code:      $request->validated('code'),
            address:   $request->validated('address'),
            city:      $request->validated('city'),
            province:  $request->validated('province'),
            isActive:  $request->has('is_active') ? (bool) $request->validated('is_active') : null,
            sortOrder: $request->has('sort_order') ? (int) $request->validated('sort_order') : null,
            notes:     $request->validated('notes'),
        );
    }
}