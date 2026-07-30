<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VerificationMismatch;

class VerificationMismatchPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('verification-mismatch.viewAny');
    }

    public function view(User $user, VerificationMismatch $mismatch): bool
    {
        return $user->can('verification-mismatch.view');
    }

    public function create(User $user): bool
    {
        return $user->can('verification-mismatch.create');
    }

    public function update(User $user, VerificationMismatch $mismatch): bool
    {
        return $user->can('verification-mismatch.update');
    }

    public function delete(User $user, VerificationMismatch $mismatch): bool
    {
        return $user->can('verification-mismatch.delete');
    }

    public function resolve(User $user, VerificationMismatch $mismatch): bool
    {
        return $user->can('verification-mismatch.resolve');
    }

    public function waive(User $user, VerificationMismatch $mismatch): bool
    {
        return $user->can('verification-mismatch.waive');
    }

    public function escalate(User $user, VerificationMismatch $mismatch): bool
    {
        return $user->can('verification-mismatch.escalate');
    }
}