<?php

namespace App\Domain\CompanyCategory\Actions;

use App\Domain\CompanyCategory\Repositories\CompanyCategoryRepository;
use App\Models\CompanyCategory;

class ToggleCompanyCategoryStatusAction
{
    public function __construct(
        private readonly CompanyCategoryRepository $repository
    ) {}

    public function execute(CompanyCategory $category): CompanyCategory
    {
        return $this->repository->update($category->id, array_filter([
            'is_active'  => ! $category->is_active,
            'updated_at' => now(),
        ], fn ($value) => $value !== null));
    }
}