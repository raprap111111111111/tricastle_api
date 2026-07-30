<?php

namespace App\Domain\Company\Actions;

use App\Domain\Company\DTOs\CreateCompanyDTO;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Models\Company;

class CreateCompanyAction
{
    public function __construct(
        private readonly CompanyRepository $repository
    ) {}

    public function execute(CreateCompanyDTO $dto): Company
    {
        return $this->repository->create([
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
        ]);
    }
}