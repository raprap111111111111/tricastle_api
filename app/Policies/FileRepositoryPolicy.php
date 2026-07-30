<?php

namespace App\Policies;

use App\Models\FileRepository;
use App\Models\User;

class FileRepositoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('file-repository.viewAny');
    }

    public function view(User $user, FileRepository $file): bool
    {
        return $user->can('file-repository.view');
    }

    public function create(User $user): bool
    {
        return $user->can('file-repository.create');
    }

    public function delete(User $user, FileRepository $file): bool
    {
        return $user->can('file-repository.delete');
    }

    public function purge(User $user, FileRepository $file): bool
    {
        return $user->can('file-repository.purge');
    }
}