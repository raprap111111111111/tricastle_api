<?php

namespace App\Domain\PassportIssuingOffice\Actions;

use App\Domain\PassportIssuingOffice\DTOs\CreatePassportIssuingOfficeDTO;
use App\Domain\PassportIssuingOffice\Repositories\PassportIssuingOfficeRepository;
use App\Models\PassportIssuingOffice;
use Illuminate\Support\Facades\DB;

class CreatePassportIssuingOfficeAction
{
    public function __construct(
        private readonly PassportIssuingOfficeRepository $repository,
    ) {}

    public function execute(CreatePassportIssuingOfficeDTO $dto): PassportIssuingOffice
    {
        return DB::transaction(function () use ($dto) {
            return $this->repository->create([
                'code'       => $dto->code,
                'region'     => $dto->region,
                'name'       => $dto->name,
                'address'    => $dto->address,
                'city'       => $dto->city,
                'province'   => $dto->province,
                'is_active'  => $dto->isActive,
                'sort_order' => $dto->sortOrder,
                'notes'      => $dto->notes,
            ]);
        });
    }
}