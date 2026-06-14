<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Exam extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'exam_code',
        'class_id',
        'session_id',
        'exam_type',
        'start_date',
        'end_date',
        'description',
        'total_marks',
        'passing_marks',
        'passing_percentage',
        'status',
        'result_published',
        'result_date'
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'result_date' => 'date',
        'total_marks' => 'decimal:2',
        'passing_marks' => 'decimal:2',
        'passing_percentage' => 'decimal:2',
        'result_published' => 'boolean',
    ];

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    public function examSubjects()
    {
        return $this->hasMany(ExamSubject::class);
    }

    public function studentMarks()
    {
        return $this->hasMany(StudentMark::class);
    }

    public function studentResults()
    {
        return $this->hasMany(StudentResult::class);
    }

    public static function generateExamCode()
    {
        $year = date('Y');
        $lastExam = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();
        
        $number = $lastExam ? (int)substr($lastExam->exam_code, -4) + 1 : 1;
        
        return "EXM/{$year}/" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    public function calculateTotalMarks()
    {
        $this->total_marks = $this->examSubjects()->sum(\DB::raw('max_marks + practical_marks'));
        $this->save();
    }

    public function scopeActive($query)
    {
        return $query->where('status', '!=', 'cancelled');
    }

    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePublished($query)
    {
        return $query->where('result_published', true);
    }
}
