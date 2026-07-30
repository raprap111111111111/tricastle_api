<?php
// app/Policies/LoginHistoryPolicy.php

namespace App\Policies;

use App\Models\LoginHistory;
use App\Models\User;

class LoginHistoryPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->can('login-history.viewAny')
            || $user->can('login-history.viewOwn');
    }

    public function view(User $user, LoginHistory $history): bool
    {
        return $user->can('login-history.viewAny')
            || ($user->can('login-history.viewOwn')
                && $history->user_id === $user->id);
    }

    public function create(User $user): bool
    {
        return $user->can('login-history.create');
    }

    public function delete(User $user, LoginHistory $history): bool
    {
        return $user->can('login-history.delete');
    }

    public function recordLogout(User $user, LoginHistory $history): bool
    {
        return $history->user_id === $user->id;
    }
}