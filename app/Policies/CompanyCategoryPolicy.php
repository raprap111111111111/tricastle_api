<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    /**
     * Perform pre-authorization checks.
     * Super admins or users with 'approval.bypass' bypass all policy checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ((method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) || $user->can('approval.bypass')) {
            return true;
        }

        return null; // Fall through to standard policy checks
    }

    public function viewAny(User $user): bool
    {
        return $user->can('applicant.viewAny');
    }

    public function view(User $user, Applicant $applicant): bool
    {
        return $user->can('applicant.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicant.create');
    }

    public function update(User $user, Applicant $applicant): bool
    {
        return $user->can('applicant.update');
    }

    public function delete(User $user, Applicant $applicant): bool
    {
        return $user->can('applicant.delete');
    }

    public function assign(User $user, Applicant $applicant): bool
    {
        return $user->can('applicant.assign');
    }

    public function transfer(User $user, Applicant $applicant): bool
    {
        return $user->can('applicant.transfer');
    }
}