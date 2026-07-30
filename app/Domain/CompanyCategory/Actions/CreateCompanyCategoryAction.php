<?php

namespace App\Domain\CompanyCategory\Actions;

use App\Domain\CompanyCategory\DTOs\CreateCompanyCategoryDTO;
use App\Domain\CompanyCategory\Repositories\CompanyCategoryRepository;
use App\Models\CompanyCategory;
use Illuminate\Support\Str;

class CreateCompanyCategoryAction
{
    public function __construct(
        private readonly CompanyCategoryRepository $repository,
    ) {}

    public function execute(CreateCompanyCategoryDTO $dto): CompanyCategory
    {
        return $this->repository->create([
            'name'        => $dto->name,
            'slug'        => $dto->slug ?: Str::slug($dto->name),
            'description' => $dto->description,
            'is_active'   => $dto->isActive,
        ]);
    }
}