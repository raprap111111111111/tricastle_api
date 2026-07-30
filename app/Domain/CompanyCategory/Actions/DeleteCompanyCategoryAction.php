<?php

namespace App\Domain\CompanyCategory\Actions;

use App\Domain\CompanyCategory\Repositories\CompanyCategoryRepository;
use App\Models\CompanyCategory;

class DeleteCompanyCategoryAction
{
    public function __construct(
        private readonly CompanyCategoryRepository $repository
    ) {}

    public function execute(CompanyCategory $category): bool
    {
        return $this->repository->delete($category->id);
    }
}