<?php

namespace App\Domain\PassportIssuingOffice\Actions;

use App\Domain\PassportIssuingOffice\Repositories\PassportIssuingOfficeRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class GetAllPassportIssuingOfficeAction
{
    public function __construct(
        private readonly PassportIssuingOfficeRepository $repository,
    ) {}

    public function execute(array $filters = []): Collection|LengthAwarePaginator
    {
        if (request()->boolean('paginate')) {
            return $this->repository->list($filters);
        }

        return $this->repository->query()->ordered()->get();
    }
}