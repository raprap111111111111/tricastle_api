<?php

namespace App\Policies;

use App\Models\ApplicantTattoo;
use App\Models\User;

class ApplicantTattooPolicy
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
        return $user->can('applicant-tattoo.viewAny');
    }

    public function view(User $user, ApplicantTattoo $applicantTattoo): bool
    {
        return $user->can('applicant-tattoo.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicant-tattoo.create');
    }

    public function update(User $user, ApplicantTattoo $applicantTattoo): bool
    {
        return $user->can('applicant-tattoo.update');
    }

    public function delete(User $user, ApplicantTattoo $applicantTattoo): bool
    {
        return $user->can('applicant-tattoo.delete');
    }

    public function restore(User $user, ApplicantTattoo $applicantTattoo): bool
    {
        return $user->can('applicant-tattoo.delete');
    }

    public function forceDelete(User $user, ApplicantTattoo $applicantTattoo): bool
    {
        return $user->can('applicant-tattoo.delete');
    }

    public function toggleVisibility(User $user, ApplicantTattoo $applicantTattoo): bool
    {
        return $user->can('applicant-tattoo.toggleVisibility');
    }
}