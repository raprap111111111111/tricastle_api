<?php

namespace App\Domain\Company\Actions;

use App\Domain\Company\DTOs\UpdateCompanyDTO;
use App\Domain\Company\Repositories\CompanyRepository;
use App\Domain\Shared\Services\TranslationService; // 🌐 ADD
use App\Models\Company;

class UpdateCompanyAction
{
    public function __construct(
        private readonly CompanyRepository $repository,
        private readonly TranslationService $translator, // 🌐 ADD
    ) {}

    public function execute(Company $company, UpdateCompanyDTO $dto): Company
    {
        // 🌐 Resolve localized name before array_filter
        $localizedName = $this->resolveLocalizedName($dto, $company);

        $payload = array_filter([
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
        ], fn ($value) => $value !== null);

        return $this->repository->update($company->id, $payload);
    }

    // 🌐 Translation logic — preserves user's array_filter behavior
    private function resolveLocalizedName(UpdateCompanyDTO $dto, Company $company): ?string
    {
        // User explicitly provided → keep it (array_filter handles null)
        if ($dto->nameJapanese !== null) {
            return $dto->nameJapanese;
        }

        $nameChanged    = $dto->name !== $company->name;
        $countryChanged = $dto->country !== $company->country;

        // Nothing relevant changed → preserve existing DB value
        // But return it so array_filter includes it; else return null to skip
        if (!$nameChanged && !$countryChanged) {
            return $company->name_japanese; // keep existing
        }

        // Translation attempt on change
        if (!blank($dto->name) && filled($dto->country)) {
            if (method_exists($this->translator, 'needsTranslation') && !$this->translator->needsTranslation($dto->country)) {
                return null;
            }

            $translated = $this->translator->translateForCountry(
                text:    $dto->name,
                country: $dto->country,
            );

            // If translation succeeds, use it; else fall back to existing or null
            return $translated ?? $company->name_japanese;
        }

        return null;
    }
}