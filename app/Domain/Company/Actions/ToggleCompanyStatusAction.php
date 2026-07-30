<?php

namespace App\Domain\Company\Actions;

use App\Domain\Company\Repositories\CompanyRepository;
use App\Models\Company;

class ToggleCompanyStatusAction
{
    public function __construct(
        private readonly CompanyRepository $repository
    ) {}

    public function execute(Company $company): Company
    {
        return $this->repository->update($company->id, [
            'is_active' => ! $company->is_active,
        ]);
    }
}