<?php

namespace App\Policies;

use App\Models\PassportIssuingOffice;
use App\Models\User;

class PassportIssuingOfficePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('passportIssuingOffice.viewAny') || $user->can('applicant.viewAny');
    }

    public function view(User $user, PassportIssuingOffice $office): bool
    {
        return $user->can('passportIssuingOffice.view') || $user->can('applicant.view');
    }

    public function create(User $user): bool
    {
        return $user->can('passportIssuingOffice.create');
    }

    public function update(User $user, PassportIssuingOffice $office): bool
    {
        return $user->can('passportIssuingOffice.update');
    }

    public function delete(User $user, PassportIssuingOffice $office): bool
    {
        return $user->can('passportIssuingOffice.delete');
    }
}