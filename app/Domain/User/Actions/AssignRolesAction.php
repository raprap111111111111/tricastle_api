<?php
// app/Domain/User/Actions/AssignRolesAction.php

namespace App\Domain\User\Actions;

use App\Models\User;

class AssignRolesAction
{
    public function execute(User $user, array $roles): User
    {
        $user->syncRoles($roles);

        return $user->fresh(['roles', 'permissions']);
    }
}