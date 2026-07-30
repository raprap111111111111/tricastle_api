<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('applicants.viewAny');
    }

    public function view(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.view');
    }

    public function create(User $user): bool
    {
        return $user->can('applicants.create');
    }

    public function update(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.update');
    }

    public function delete(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.delete');
    }

    public function assign(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.assign');
    }

    public function transfer(User $user, Applicant $applicant): bool
    {
        return $user->can('applicants.transfer');
    }
}
