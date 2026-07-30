<?php

namespace App\Policies;

use App\Models\DocumentVerification;
use App\Models\User;

class DocumentVerificationPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('document-verification.viewAny');
    }

    public function view(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.view');
    }

    public function create(User $user): bool
    {
        return $user->can('document-verification.create');
    }

    public function update(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.update');
    }

    public function delete(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.delete');
    }

    public function start(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.start');
    }

    public function complete(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.complete');
    }

    public function approve(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.approve');
    }

    public function reject(User $user, DocumentVerification $verification): bool
    {
        return $user->can('document-verification.reject');
    }
}