<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Hanya Super Admin yang bisa manage roles.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('manage-roles');
    }

    public function view(User $user, Role $role): bool
    {
        return $user->hasPermissionTo('manage-roles');
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo('manage-roles');
    }

    public function update(User $user, Role $role): bool
    {
        // Super Admin role tidak bisa diubah namanya
        if ($role->name === 'super admin') {
            return false;
        }

        return $user->hasPermissionTo('manage-roles');
    }

    public function delete(User $user, Role $role): bool
    {
        // Super Admin role tidak boleh dihapus
        if ($role->name === 'super admin') {
            return false;
        }

        // Role yang masih punya user tidak bisa dihapus
        if ($role->users()->count() > 0) {
            return false;
        }

        return $user->hasPermissionTo('manage-roles');
    }
}
