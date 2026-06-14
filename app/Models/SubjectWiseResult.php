<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SubjectWiseResult extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_result_id',
        'subject_id',
        'marks_obtained',
        'max_marks',
        'percentage',
        'grade',
        'status'
    ];

    protected $casts = [
        'marks_obtained' => 'decimal:2',
        'max_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
    ];

    public function studentResult()
    {
        return $this->belongsTo(StudentResult::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
}
