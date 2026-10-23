<?php

namespace App\Domain\PassportIssuingOffice\Actions;

use App\Domain\PassportIssuingOffice\Repositories\PassportIssuingOfficeRepository;
use App\Models\PassportIssuingOffice;

class DeletePassportIssuingOfficeAction
{
    public function __construct(
        private readonly PassportIssuingOfficeRepository $repository,
    ) {}

    public function execute(PassportIssuingOffice $office): bool
    {
        return $this->repository->delete($office);
    }
}