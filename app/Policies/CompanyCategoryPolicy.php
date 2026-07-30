<?php

namespace App\Policies;

use App\Models\CompanyCategory;
use App\Models\User;

class CompanyCategoryPolicy
{
    /**
     * Global bypass — super admins can do anything.
     * Adjust `isSuperAdmin()` to match your user schema.
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
        return $user->can('company_category.view');
    }

    public function view(User $user, CompanyCategory $category): bool
    {
        return $user->can('company_category.view');
    }

    public function create(User $user): bool
    {
        return $user->can('company_category.create');
    }

    public function update(User $user, CompanyCategory $category): bool
    {
        return $user->can('company_category.update');
    }

    public function delete(User $user, CompanyCategory $category): bool
    {
        return $user->can('company_category.delete');
    }

    public function restore(User $user, CompanyCategory $category): bool
    {
        return $user->can('company_category.delete');
    }

    public function forceDelete(User $user, CompanyCategory $category): bool
    {
        return $user->can('company_category.delete');
    }
}