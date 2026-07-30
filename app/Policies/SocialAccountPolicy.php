<?php
// app/Policies/SocialAccountPolicy.php

namespace App\Policies;

use App\Models\SocialAccount;
use App\Models\User;

class SocialAccountPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('social-account.viewAny')
            || $user->can('social-account.viewOwn');
    }

    public function view(User $user, SocialAccount $socialAccount): bool
    {
        return $user->can('social-account.viewAny')
            || ($user->can('social-account.viewOwn')
                && $socialAccount->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('social-account.create');
    }

    public function delete(User $user, SocialAccount $socialAccount): bool
    {
        return $user->can('social-account.delete')
            || $socialAccount->user_id === $user->id;
    }
}