<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PerformanceAnalytics extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'exam_id',
        'subject_id',
        'average_marks',
        'highest_marks',
        'lowest_marks',
        'subject_rank'
    ];

    protected $casts = [
        'average_marks' => 'decimal:2',
        'highest_marks' => 'decimal:2',
        'lowest_marks' => 'decimal:2',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
