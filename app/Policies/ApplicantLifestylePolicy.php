<?php

namespace App\Policies;

use App\Models\ApplicantLifestyle;
use App\Models\User;

class ApplicantLifestylePolicy
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
        return $user->can('applicant-lifestyle.viewAny');
    }

    public function view(User $user, ApplicantLifestyle $applicantLifestyle): bool
    {
        return $user->can('applicant-lifestyle.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicant-lifestyle.create');
    }

    public function update(User $user, ApplicantLifestyle $applicantLifestyle): bool
    {
        return $user->can('applicant-lifestyle.update');
    }

    public function delete(User $user, ApplicantLifestyle $applicantLifestyle): bool
    {
        return $user->can('applicant-lifestyle.delete');
    }

    public function restore(User $user, ApplicantLifestyle $applicantLifestyle): bool
    {
        return $user->can('applicant-lifestyle.delete');
    }

    public function forceDelete(User $user, ApplicantLifestyle $applicantLifestyle): bool
    {
        return $user->can('applicant-lifestyle.delete');
    }
}