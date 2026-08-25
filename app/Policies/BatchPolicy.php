<?php

namespace App\Policies;

use App\Models\Batch;
use App\Models\User;

class BatchPolicy
{
    /**
     * Global bypass — super admins can do anything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return null; // fall through to normal checks
    }

    public function viewAny(User $user): bool
    {
        return $user->can('batch.viewAny');
    }

    public function view(User $user, Batch $batch): bool
    {
        return $user->can('batch.view');
    }

    public function create(User $user): bool
    {
        return $user->can('batch.create');
    }

    public function update(User $user, Batch $batch): bool
    {
        return $user->can('batch.update');
    }

    public function delete(User $user, Batch $batch): bool
    {
        return $user->can('batch.delete');
    }

    public function restore(User $user, Batch $batch): bool
    {
        return $user->can('batch.delete');
    }

    public function forceDelete(User $user, Batch $batch): bool
    {
        return $user->can('batch.delete');
    }

    public function updateStatus(User $user, Batch $batch): bool
    {
        return $user->can('batch.updateStatus');
    }

    public function manageSlots(User $user, Batch $batch): bool
    {
        return $user->can('batch.manageSlots');
    }
}