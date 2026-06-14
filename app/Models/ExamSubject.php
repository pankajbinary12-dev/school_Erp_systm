<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ExamSubject extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'exam_schedules'; // Using existing table

    protected $fillable = [
        'exam_id',
        'subject_id',
        'max_marks',
        'practical_marks',
        'passing_marks',
        'exam_order',
        'exam_date',
        'start_time',
        'end_time'
    ];

    protected $casts = [
        'max_marks' => 'decimal:2',
        'practical_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'exam_date' => 'date',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function studentMarks()
    {
        return $this->hasMany(StudentMark::class, 'exam_schedule_id');
    }

    public function getTotalMaxMarksAttribute()
    {
        return $this->max_marks + $this->practical_marks;
    }
}
