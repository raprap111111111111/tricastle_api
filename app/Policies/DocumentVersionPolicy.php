<?php

namespace App\Policies;

use App\Models\DocumentVersion;
use App\Models\User;

class DocumentVersionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('document-version.viewAny');
    }

    public function view(User $user, DocumentVersion $documentVersion): bool
    {
        return $user->can('document-version.view');
    }

    public function create(User $user): bool
    {
        return $user->can('document-version.create');
    }

    public function delete(User $user, DocumentVersion $documentVersion): bool
    {
        return $user->can('document-version.delete');
    }

    public function setCurrent(User $user, DocumentVersion $documentVersion): bool
    {
        return $user->can('document-version.set-current');
    }
}