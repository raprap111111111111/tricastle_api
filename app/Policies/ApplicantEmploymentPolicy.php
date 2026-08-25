<?php

namespace App\Policies;

use App\Models\ApplicantEmployment;
use App\Models\User;

class ApplicantEmploymentPolicy
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
        return $user->can('applicant-employment.viewAny');
    }

    public function view(User $user, ApplicantEmployment $applicantEmployment): bool
    {
        return $user->can('applicant-employment.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicant-employment.create');
    }

    public function update(User $user, ApplicantEmployment $applicantEmployment): bool
    {
        return $user->can('applicant-employment.update');
    }

    public function delete(User $user, ApplicantEmployment $applicantEmployment): bool
    {
        return $user->can('applicant-employment.delete');
    }

    public function restore(User $user, ApplicantEmployment $applicantEmployment): bool
    {
        return $user->can('applicant-employment.delete');
    }

    public function forceDelete(User $user, ApplicantEmployment $applicantEmployment): bool
    {
        return $user->can('applicant-employment.delete');
    }

    public function markAsCurrent(User $user, ApplicantEmployment $applicantEmployment): bool
    {
        return $user->can('applicant-employment.markAsCurrent');
    }
}