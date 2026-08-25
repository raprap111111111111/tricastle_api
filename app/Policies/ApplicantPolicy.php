<?php

namespace App\Policies;

use App\Models\Applicant;
use App\Models\User;

class ApplicantPolicy
{
    public function viewAny(User $user): bool
    {
        // Changed from 'applicants.viewAny' to 'applicant.viewAny'
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