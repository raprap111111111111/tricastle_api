<?php

namespace App\Domain\Company\Actions;

use App\Domain\Company\DTOs\CreateCompanyDTO;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Shared\Services\TranslationService; // 🌐 ADD
use App\Models\Company;

class CreateCompanyAction
{
    public function __construct(
        private readonly CompanyRepository $repository,
        private readonly TranslationService $translator, // 🌐 ADD
    ) {}

    public function execute(CreateCompanyDTO $dto): Company
    {
        // 🌐 Resolve localized name (user-provided wins, else translate)
        $localizedName = $this->resolveLocalizedName($dto);

        return $this->repository->create([
            'code'           => $dto->code,
            'name'           => $dto->name,
            'name_japanese'  => $localizedName, // ← was $dto->nameJapanese
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

    // 🌐 Translation logic only — nothing else added
    private function resolveLocalizedName(CreateCompanyDTO $dto): ?string
    {
        if (filled($dto->nameJapanese)) {
            return $dto->nameJapanese;
        }

        if (blank($dto->name) || blank($dto->country)) {
            return null;
        }

        if (!method_exists($this->translator, 'needsTranslation') || !$this->translator->needsTranslation($dto->country)) {
            return null;
        }

        return $this->translator->translateForCountry(
            text:    $dto->name,
            country: $dto->country,
        );
    }
}