<?php

namespace App\Domain\BatchGroup\Actions;

use App\Domain\BatchGroup\DTOs\UpdateBatchGroupDTO;
use App\Models\BatchGroup;

class UpdateBatchGroupAction
{
    public function execute(BatchGroup $group, UpdateBatchGroupDTO $dto): BatchGroup
    {
        $payload = array_filter([
            'name'      => $dto->name,
            'color'     => $dto->color,
            'max_slots' => $dto->maxSlots,
        ], fn($value) => $value !== null);

        $group->update($payload);

        return $group->fresh();
    }
}
