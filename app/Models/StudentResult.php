<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentResult extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'exam_id',
        'student_id',
        'total_marks_obtained',
        'total_max_marks',
        'percentage',
        'grade',
        'rank',
        'total_students',
        'result',
        'remarks',
        'is_published'
    ];

    protected $casts = [
        'total_marks_obtained' => 'decimal:2',
        'total_max_marks' => 'decimal:2',
        'percentage' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    public function exam()
    {
        return $this->belongsTo(Exam::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function calculateResult()
    {
        // Get all marks for this student in this exam
        $marks = StudentMark::where('exam_id', $this->exam_id)
            ->where('student_id', $this->student_id)
            ->where('status', 'present')
            ->get();

        if ($marks->isEmpty()) {
            $this->result = 'absent';
            $this->save();
            return;
        }

        // Calculate totals
        $this->total_marks_obtained = $marks->sum('total_marks');
        $this->total_max_marks = $marks->sum(function($mark) {
            return $mark->examSubject->total_max_marks;
        });

        // Calculate percentage
        $this->percentage = $this->total_max_marks > 0 
            ? ($this->total_marks_obtained / $this->total_max_marks) * 100 
            : 0;

        // Determine grade
        $this->grade = $this->calculateGrade($this->percentage);

        // Check if passed (all subjects passed and overall percentage >= passing percentage)
        $allSubjectsPassed = $marks->every(function($mark) {
            return $mark->is_passed;
        });

        $exam = $this->exam;
        $overallPassed = $this->percentage >= $exam->passing_percentage;

        $this->result = ($allSubjectsPassed && $overallPassed) ? 'pass' : 'fail';

        $this->save();
    }

    public function calculateGrade($percentage)
    {
        $grade = GradeSystem::where('status', 'active')
            ->where('min_percentage', '<=', $percentage)
            ->where('max_percentage', '>=', $percentage)
            ->orderBy('display_order')
            ->first();

        return $grade ? $grade->grade : 'F';
    }

    public function calculateRank()
    {
        $exam = $this->exam;
        
        // Get all results for this exam, ordered by percentage
        $results = StudentResult::where('exam_id', $this->exam_id)
            ->where('result', 'pass')
            ->orderBy('percentage', 'desc')
            ->orderBy('total_marks_obtained', 'desc')
            ->get();

        $this->total_students = $results->count();

        // Find rank
        $rank = 1;
        foreach ($results as $result) {
            if ($result->id == $this->id) {
                $this->rank = $rank;
                break;
            }
            $rank++;
        }

        $this->save();
    }

    public function scopePass($query)
    {
        return $query->where('result', 'pass');
    }

    public function scopeFail($query)
    {
        return $query->where('result', 'fail');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true);
    }
}
