<?php

namespace App\Policies;

use App\Models\CompanyCategory;
use App\Models\User;

class CompanyCategoryPolicy
{
    /**
     * Global bypass — super admins can do anything.
     */
    public function before(User $user, string $ability): ?bool
    {
        if (method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            return true;
        }

        return null; // fall through to normal checks
    }

    public function viewAny(User $user): bool
    {
        // Changed _ to - and view to viewAny
        return $user->can('company-category.viewAny');
    }

    public function view(User $user, CompanyCategory $category): bool
    {
        return $user->can('company-category.view');
    }

    public function create(User $user): bool
    {
        return $user->can('company-category.create');
    }

    public function update(User $user, CompanyCategory $category): bool
    {
        return $user->can('company-category.update');
    }

    public function delete(User $user, CompanyCategory $category): bool
    {
        return $user->can('company-category.delete');
    }

    public function restore(User $user, CompanyCategory $category): bool
    {
        return $user->can('company-category.delete');
    }

    public function forceDelete(User $user, CompanyCategory $category): bool
    {
        return $user->can('company-category.delete');
    }
}