<?php

namespace App\Policies;

use App\Models\CorrectionRequest;
use App\Models\User;

class CorrectionRequestPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('correction.viewAny');
    }

    public function view(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.view');
    }

    public function create(User $user): bool
    {
        return $user->can('correction.create');
    }

    public function update(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.update');
    }

    public function delete(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.delete');
    }

    public function approve(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.approve');
    }

    public function reject(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.reject');
    }

    public function complete(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.complete');
    }

    public function cancel(User $user, CorrectionRequest $correctionRequest): bool
    {
        return $user->can('correction.cancel');
    }
}