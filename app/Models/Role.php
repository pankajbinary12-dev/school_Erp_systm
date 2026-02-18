<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Role extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'guard_name',
        'is_active'
    ];

    protected $attributes = [
        'guard_name' => 'admin'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'role_permissions');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_roles');
    }

    public function admins()
    {
        return $this->belongsToMany(Admin::class, 'admin_roles');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_roles');
    }

    public function staffMembers()
    {
        return $this->belongsToMany(StaffMember::class, 'staff_roles', 'role_id', 'staff_id');
    }

    // Helper Methods
    public function hasPermission($permission)
    {
        return $this->permissions()->where('name', $permission)->exists();
    }

    public function givePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        return $this->permissions()->syncWithoutDetaching($permission);
    }

    public function revokePermissionTo($permission)
    {
        if (is_string($permission)) {
            $permission = Permission::where('name', $permission)->firstOrFail();
        }
        return $this->permissions()->detach($permission);
    }
}
