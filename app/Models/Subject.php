<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'subject_name',
        'subject_code',
        'description',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'string'
    ];

    // Accessor for name (maps to subject_name)
    public function getNameAttribute()
    {
        return $this->subject_name;
    }

    public function classes()
    {
        return $this->belongsToMany(Classes::class, 'class_subjects', 'subject_id', 'class_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects', 'subject_id', 'teacher_id');
    }
}
