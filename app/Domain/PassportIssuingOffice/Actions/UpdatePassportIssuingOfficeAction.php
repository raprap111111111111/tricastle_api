<?php

namespace App\Domain\PassportIssuingOffice\Actions;

use App\Domain\PassportIssuingOffice\DTOs\UpdatePassportIssuingOfficeDTO;
use App\Domain\PassportIssuingOffice\Repositories\PassportIssuingOfficeRepository;
use App\Models\PassportIssuingOffice;
use Illuminate\Support\Facades\DB;

class UpdatePassportIssuingOfficeAction
{
    public function __construct(
        private readonly PassportIssuingOfficeRepository $repository,
    ) {}

    public function execute(PassportIssuingOffice $office, UpdatePassportIssuingOfficeDTO $dto): PassportIssuingOffice
    {
        return DB::transaction(function () use ($office, $dto) {
            $data = array_filter([
                'region'     => $dto->region,
                'name'       => $dto->name,
                'code'       => $dto->code,
                'address'    => $dto->address,
                'city'       => $dto->city,
                'province'   => $dto->province,
                'is_active'  => $dto->isActive,
                'sort_order' => $dto->sortOrder,
                'notes'      => $dto->notes,
            ], fn($v) => $v !== null);

            $this->repository->update($office, $data);

            return $office->fresh();
        });
    }
}