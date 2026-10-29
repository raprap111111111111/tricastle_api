<?php

namespace App\Domain\BatchGroup\Actions;

use App\Models\BatchGroup;

class DeleteBatchGroupAction
{
    public function execute(BatchGroup $group): bool
    {
        return (bool) $group->delete();
    }
}
