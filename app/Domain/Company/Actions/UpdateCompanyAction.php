<?php

namespace App\Domain\Company\Actions;

use App\Domain\Company\DTOs\UpdateCompanyDTO;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Models\Company;

class UpdateCompanyAction
{
    public function __construct(
        private readonly CompanyRepository $repository
    ) {}

    public function execute(Company $company, UpdateCompanyDTO $dto): Company
    {
        $payload = array_filter([
            'code'           => $dto->code,
            'name'           => $dto->name,
            'name_japanese'  => $dto->nameJapanese,
            'category_id'    => $dto->categoryId,
            'address'        => $dto->address,
            'city'           => $dto->city,
            'prefecture'     => $dto->prefecture,
            'postal_code'    => $dto->postalCode,
            'country'        => $dto->country,
            'contact_person' => $dto->contactPerson,
            'contact_email'  => $dto->contactEmail,
            'contact_phone'  => $dto->contactPhone,
            'description'    => $dto->description,
            'is_active'      => $dto->isActive,
        ], fn ($value) => $value !== null);

        return $this->repository->update($company->id, $payload);
    }
}