<?php

namespace App\Policies;

use App\Models\ApplicantEducation;
use App\Models\User;

class ApplicantEducationPolicy
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
        return $user->can('applicant-education.viewAny');
    }

    public function view(User $user, ApplicantEducation $applicantEducation): bool
    {
        return $user->can('applicant-education.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicant-education.create');
    }

    public function update(User $user, ApplicantEducation $applicantEducation): bool
    {
        return $user->can('applicant-education.update');
    }

    public function delete(User $user, ApplicantEducation $applicantEducation): bool
    {
        return $user->can('applicant-education.delete');
    }

    public function restore(User $user, ApplicantEducation $applicantEducation): bool
    {
        return $user->can('applicant-education.delete');
    }

    public function forceDelete(User $user, ApplicantEducation $applicantEducation): bool
    {
        return $user->can('applicant-education.delete');
    }
}