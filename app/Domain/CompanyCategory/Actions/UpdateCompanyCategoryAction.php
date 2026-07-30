<?php

namespace App\Domain\CompanyCategory\Actions;

use App\Domain\CompanyCategory\DTOs\UpdateCompanyCategoryDTO;
use App\Domain\CompanyCategory\Repositories\CompanyCategoryRepository;
use App\Models\CompanyCategory;
use Illuminate\Support\Str;

class UpdateCompanyCategoryAction
{
    public function __construct(
        private readonly CompanyCategoryRepository $repository
    ) {}

    public function execute(CompanyCategory $category, UpdateCompanyCategoryDTO $dto): CompanyCategory
    {
        $payload = array_filter([
            'name'        => $dto->name,
            'slug'        => $dto->slug ?: ($dto->name ? Str::slug($dto->name) : null),
            'description' => $dto->description,
            'is_active'   => $dto->isActive,
        ], fn ($value) => $value !== null);

        return $this->repository->update($category->id, $payload);
    }
}