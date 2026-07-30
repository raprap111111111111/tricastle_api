<?php

namespace App\Policies;

use App\Models\ApplicantDocument;
use App\Models\User;

class ApplicantDocumentPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('document.viewAny');
    }

    public function view(User $user, ApplicantDocument $document): bool
    {
        return $user->can('document.view');
    }

    public function create(User $user): bool
    {
        return $user->can('document.create');
    }

    public function update(User $user, ApplicantDocument $document): bool
    {
        return $user->can('document.update');
    }

    public function delete(User $user, ApplicantDocument $document): bool
    {
        return $user->can('document.delete');
    }

    public function verify(User $user, ApplicantDocument $document): bool
    {
        return $user->can('document.verifyAny');
    }

    public function reject(User $user, ApplicantDocument $document): bool
    {
        return $user->can('document.reject');
    }
}