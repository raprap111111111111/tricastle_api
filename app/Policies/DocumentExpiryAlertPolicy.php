<?php
// app/Policies/DocumentExpiryAlertPolicy.php

namespace App\Policies;

use App\Models\DocumentExpiryAlert;
use App\Models\User;

class DocumentExpiryAlertPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('document-expiry-alert.viewAny');
    }

    public function view(User $user, DocumentExpiryAlert $alert): bool
    {
        return $user->can('document-expiry-alert.viewAny');
    }

    public function create(User $user): bool
    {
        return $user->can('document-expiry-alert.create');
    }

    public function delete(User $user, DocumentExpiryAlert $alert): bool
    {
        return $user->can('document-expiry-alert.delete');
    }

    public function dismiss(User $user, DocumentExpiryAlert $alert): bool
    {
        return $user->can('document-expiry-alert.dismiss');
    }
}