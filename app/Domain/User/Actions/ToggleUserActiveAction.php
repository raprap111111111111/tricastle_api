<?php
// app/Domain/User/Actions/ToggleUserActiveAction.php

namespace App\Domain\User\Actions;

use App\Models\User;

class ToggleUserActiveAction
{
    public function execute(User $user): User
    {
        $user->update(['is_active' => !$user->is_active]);

        return $user->fresh(['roles']);
    }
}