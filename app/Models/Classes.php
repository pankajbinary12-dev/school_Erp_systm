<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classes extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'class_name',
        'class_numeric',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'string'
    ];

    // Accessor for name (for backward compatibility)
    public function getNameAttribute()
    {
        return $this->class_name;
    }

    public function sections()
    {
        return $this->hasMany(Section::class, 'class_id');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }

    public function subjects()
    {
        return $this->belongsToMany(Subject::class, 'class_subjects', 'class_id', 'subject_id');
    }

    public function teachers()
    {
        return $this->belongsToMany(Teacher::class, 'teacher_subjects', 'class_id', 'teacher_id');
    }
}
