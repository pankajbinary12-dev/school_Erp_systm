<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasRolesAndPermissions;

class StaffMember extends Model
{
    use HasRolesAndPermissions, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pin_code',
        'qualification',
        'designation',
        'department',
        'joining_date',
        'salary',
        'photo',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2'
    ];

    // Roles relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'staff_roles', 'staff_id', 'role_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }
}
