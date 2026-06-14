<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentMark extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_marks'; // Using existing table

    protected $fillable = [
        'exam_id',
        'exam_schedule_id', // This is exam_subject_id
        'student_id',
        'theory_marks',
        'practical_marks',
        'total_marks',
        'status',
        'is_passed',
        'remarks',
        'entered_by',
        'entered_at'
    ];

    protected $casts = [
        'theory_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'total_marks' => 'decimal:2',
        'is_passed' => 'boolean',
        'entered_at' => 'datetime',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function examSubject()
    {
        return $this->belongsTo(ExamSubject::class, 'exam_schedule_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function enteredBy()
    {
        return $this->belongsTo(Admin::class, 'entered_by');
    }

    public function calculateTotal()
    {
        $this->total_marks = ($this->theory_marks ?? 0) + ($this->practical_marks ?? 0);
        $this->is_passed = $this->total_marks >= $this->examSubject->passing_marks;
        $this->save();
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'absent');
    }
}
