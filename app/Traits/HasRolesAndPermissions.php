<?php

namespace App\Traits;

use App\Models\Role;
use App\Models\Permission;

trait HasRolesAndPermissions
{
    // Check if user has a specific role
    public function hasRole($role)
    {
        if (is_string($role)) {
            return $this->roles()->where('name', $role)->exists();
        }
        return $this->roles()->where('id', $role->id)->exists();
    }

    // Check if user has any of the given roles
    public function hasAnyRole($roles)
    {
        if (is_string($roles)) {
            return $this->roles()->where('name', $roles)->exists();
        }
        if (is_array($roles)) {
            return $this->roles()->whereIn('name', $roles)->exists();
        }
        return false;
    }

    // Check if user has all of the given roles
    public function hasAllRoles($roles)
    {
        if (is_array($roles)) {
            foreach ($roles as $role) {
                if (!$this->hasRole($role)) {
                    return false;
                }
            }
            return true;
        }
        return false;
    }

    // Assign a role to user
    public function assignRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        return $this->roles()->syncWithoutDetaching($role);
    }

    // Remove a role from user
    public function removeRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        return $this->roles()->detach($role);
    }

    // Check if user has a specific permission (through roles)
    public function hasPermission($permission)
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    // Check if user has any of the given permissions
    public function hasAnyPermission($permissions)
    {
        if (is_array($permissions)) {
            foreach ($permissions as $permission) {
                if ($this->hasPermission($permission)) {
                    return true;
                }
            }
        }
        return false;
    }

    // Get all permissions through roles
    public function getAllPermissions()
    {
        $permissions = collect();
        foreach ($this->roles as $role) {
            $permissions = $permissions->merge($role->permissions);
        }
        return $permissions->unique('id');
    }
}
