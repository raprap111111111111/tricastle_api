<?php

namespace App\Policies;

use App\Models\DocumentType;
use App\Models\User;

class DocumentTypePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('document-type.viewAny');
    }

    public function view(User $user, DocumentType $documentType): bool
    {
        return $user->can('document-type.view');
    }

    public function create(User $user): bool
    {
        return $user->can('document-type.create');
    }

    public function update(User $user, DocumentType $documentType): bool
    {
        return $user->can('document-type.update');
    }

    public function delete(User $user, DocumentType $documentType): bool
    {
        return $user->can('document-type.delete');
    }
}