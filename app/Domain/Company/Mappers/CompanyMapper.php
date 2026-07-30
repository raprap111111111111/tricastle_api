<?php

// app/Domain/Company/Mappers/CompanyMapper.php

namespace App\Domain\Company\Mappers;

use App\Domain\Company\DTOs\CreateCompanyDTO;
use App\Domain\Company\DTOs\UpdateCompanyDTO;
use App\Http\Requests\v1\Company\StoreCompanyRequest;
use App\Http\Requests\v1\Company\UpdateCompanyRequest;

class CompanyMapper
{
    public static function fromStoreRequest(StoreCompanyRequest $request): CreateCompanyDTO
    {
        return new CreateCompanyDTO(
            code:          $request->validated('code'),
            name:          $request->validated('name'),
            categoryId:    (int) $request->validated('category_id'),
            nameJapanese:  $request->validated('name_japanese'),
            address:       $request->validated('address'),
            city:          $request->validated('city'),
            prefecture:    $request->validated('prefecture'),
            postalCode:    $request->validated('postal_code'),
            country:       $request->validated('country', 'Japan'),
            contactPerson: $request->validated('contact_person'),
            contactEmail:  $request->validated('contact_email'),
            contactPhone:  $request->validated('contact_phone'),
            description:   $request->validated('description'),
            isActive:      (bool) $request->validated('is_active', true),
        );
    }

    public static function fromUpdateRequest(UpdateCompanyRequest $request): UpdateCompanyDTO
    {
        return new UpdateCompanyDTO(
            code:          $request->validated('code'),
            name:          $request->validated('name'),
            nameJapanese:  $request->validated('name_japanese'),
            categoryId:    $request->validated('category_id') !== null
                             ? (int) $request->validated('category_id')
                             : null,
            address:       $request->validated('address'),
            city:          $request->validated('city'),
            prefecture:    $request->validated('prefecture'),
            postalCode:    $request->validated('postal_code'),
            country:       $request->validated('country'),
            contactPerson: $request->validated('contact_person'),
            contactEmail:  $request->validated('contact_email'),
            contactPhone:  $request->validated('contact_phone'),
            description:   $request->validated('description'),
            isActive:      $request->has('is_active')
                             ? (bool) $request->validated('is_active')
                             : null,
        );
    }
}