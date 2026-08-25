<?php

namespace App\Policies;

use App\Models\User;

class DeploymentPolicy
{
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return null;
    }

    public function viewAny(User $user): bool
    {
        return $user->can('deployment.viewAny');
    }

    public function view(User $user): bool
    {
        return $user->can('deployment.view');
    }

    public function create(User $user): bool
    {
        return $user->can('deployment.update');
    }

    public function update(User $user): bool
    {
        return $user->can('deployment.update');
    }

    public function cancel(User $user): bool
    {
        return $user->can('deployment.cancel');
    }

    public function bulk(User $user): bool
    {
        return $user->can('deployment.bulk');
    }
}