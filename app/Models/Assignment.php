<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Assignment extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'section_id',
        'subject_id',
        'title',
        'description',
        'assigned_date',
        'due_date',
        'total_marks',
        'status',
        'attachment',
        'instructions'
    ];

    protected $casts = [
        'assigned_date' => 'date',
        'due_date' => 'date',
    ];

    // Relationships
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

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

    public function submissions()
    {
        return $this->hasMany(AssignmentSubmission::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeForTeacher($query, $teacherId)
    {
        return $query->where('teacher_id', $teacherId);
    }

    // Accessors
    public function getIsOverdueAttribute()
    {
        return $this->due_date < now() && $this->status === 'Active';
    }

    public function getSubmissionCountAttribute()
    {
        return $this->submissions()->where('status', '!=', 'Pending')->count();
    }

    public function getTotalStudentsAttribute()
    {
        // Get total students in class/section
        $query = \App\Models\Student::where('class_id', $this->class_id);
        if ($this->section_id) {
            $query->where('section_id', $this->section_id);
        }
        return $query->count();
    }

    public function getSubmissionPercentageAttribute()
    {
        $total = $this->total_students;
        if ($total == 0) return 0;
        return round(($this->submission_count / $total) * 100, 2);
    }
}
