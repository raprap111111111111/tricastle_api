<?php

namespace App\Domain\Company\Actions;

use App\Domain\Company\Repositories\CompanyRepository;
use App\Models\Company;

class DeleteCompanyAction
{
    public function __construct(
        private readonly CompanyRepository $repository
    ) {}

    public function execute(Company $company): bool
    {
        return $this->repository->delete($company->id);
    }
}