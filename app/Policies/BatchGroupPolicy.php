<?php

namespace App\Policies;

use App\Models\BatchGroup;
use App\Models\User;

class BatchGroupPolicy
{

    public function viewAny(User $user): bool
    {
        return $user->can('batchGroup.viewAny');
    }

    public function view(User $user, BatchGroup $group): bool
    {
        return $user->can('batchGroup.view');
    }

    public function create(User $user): bool
    {
        return $user->can('batchGroup.create');
    }

    public function update(User $user, BatchGroup $group): bool
    {
        return $user->can('batchGroup.update');
    }

    public function delete(User $user, BatchGroup $group): bool
    {
        return $user->can('batchGroup.delete');
    }

    public function assign(User $user, BatchGroup $group): bool
    {
        return $user->can('batchGroup.assign');
    }
}