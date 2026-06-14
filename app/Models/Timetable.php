<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Timetable extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'class_id',
        'section_id',
        'subject_id',
        'teacher_id',
        'day',
        'start_time',
        'end_time',
        'room_number',
        'is_active'
    ];

    protected $casts = [
        'start_time' => 'datetime:H:i',
        'end_time' => 'datetime:H:i'
    ];

    // Relationships
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', 'Active');
    }

    public function scopeForClass($query, $classId, $sectionId)
    {
        return $query->where('class_id', $classId)
                     ->where('section_id', $sectionId);
    }

    public function scopeForDay($query, $day)
    {
        return $query->where('day', $day);
    }
}
