<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'display_name',
        'description',
        'module',
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
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions');
    }

    // Helper Methods
    public function assignToRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        return $this->roles()->syncWithoutDetaching($role);
    }

    public function removeFromRole($role)
    {
        if (is_string($role)) {
            $role = Role::where('name', $role)->firstOrFail();
        }
        return $this->roles()->detach($role);
    }
}
