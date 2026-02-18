<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Traits\HasRolesAndPermissions;

class Admin extends Authenticatable
{
    use HasFactory, Notifiable, HasRolesAndPermissions, SoftDeletes;

    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'phone',
        'role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'string',
    ];

    // Roles relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_roles');
    }
}
