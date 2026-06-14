<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use App\Traits\HasRolesAndPermissions;

class Teacher extends Authenticatable
{
    use HasFactory, HasRolesAndPermissions, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'qualification',
        'joining_date',
        'username',
        'password',
        'photo',
        'status'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
    ];

    public function subjects()
    {
        return $this->hasMany(\DB::table('teacher_subjects')->getModel(), 'teacher_id');
    }

    public function assignedClasses()
    {
        return $this->hasMany(\App\Models\TeacherSubject::class, 'teacher_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Roles relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'teacher_roles');
    }
}
