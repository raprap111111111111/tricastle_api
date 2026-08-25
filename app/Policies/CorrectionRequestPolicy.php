<?php

namespace App\Policies;

use App\Models\CorrectionRequest;
use App\Models\User;

class CorrectionRequestPolicy
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
        return $user->can('correction-request.viewAny');
    }

    public function view(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.view');
    }

    public function create(User $user): bool
    {
        return $user->can('correction-request.create');
    }

    public function update(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.update');
    }

    public function delete(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.delete');
    }

    public function approve(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.approve');
    }

    public function reject(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.reject');
    }

    public function complete(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.complete');
    }

    public function cancel(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction-request.cancel');
    }
}