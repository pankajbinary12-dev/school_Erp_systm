<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AssignmentSubmission extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'submission_text',
        'file_path',
        'submitted_at',
        'status',
        'marks_obtained',
        'teacher_feedback'
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'marks_obtained' => 'decimal:2'
    ];

    // Relationships
    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    // Check if late
    public function isLate()
    {
        if ($this->submitted_at && $this->assignment->due_date) {
            return $this->submitted_at->greaterThan($this->assignment->due_date);
        }
        return false;
    }

    // Check if pending
    public function isPending()
    {
        return $this->status === 'pending' && !$this->submitted_at;
    }
}
