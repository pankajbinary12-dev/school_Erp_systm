<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Student extends Authenticatable
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'admission_no',
        'roll_no',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'father_name',
        'mother_name',
        'guardian_phone',
        'class_id',
        'section_id',
        'session_id',
        'username',
        'password',
        'photo',
        'status',
        'admission_date',
        'blood_group',
        'notification_settings'
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'admission_date' => 'date',
    ];

    protected $appends = ['name', 'full_name'];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Accessor for name (maps to full_name)
    public function getNameAttribute()
    {
        return $this->full_name;
    }
}
