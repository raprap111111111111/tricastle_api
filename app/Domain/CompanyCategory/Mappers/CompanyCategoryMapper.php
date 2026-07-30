<?php

// app/Domain/CompanyCategory/Mappers/CompanyCategoryMapper.php

namespace App\Domain\CompanyCategory\Mappers;

use App\Domain\CompanyCategory\DTOs\CreateCompanyCategoryDTO;
use App\Domain\CompanyCategory\DTOs\UpdateCompanyCategoryDTO;
use App\Http\Requests\v1\CompanyCategory\StoreCompanyCategoryRequest;
use App\Http\Requests\v1\CompanyCategory\UpdateCompanyCategoryRequest;

class CompanyCategoryMapper
{
    public static function fromStoreRequest(StoreCompanyCategoryRequest $request): CreateCompanyCategoryDTO
    {
        return new CreateCompanyCategoryDTO(
            name:        $request->validated('name'),
            slug:        $request->validated('slug'),
            description: $request->validated('description'),
            isActive:    (bool) $request->validated('is_active', true),
        );
    }

    public static function fromUpdateRequest(UpdateCompanyCategoryRequest $request): UpdateCompanyCategoryDTO
    {
        return new UpdateCompanyCategoryDTO(
            name:        $request->validated('name'),
            slug:        $request->validated('slug'),
            description: $request->validated('description'),
            isActive:    $request->has('is_active') ? (bool) $request->validated('is_active') : null,
        );
    }
}