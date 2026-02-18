<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StudentAttendance extends Model
{
    use SoftDeletes;

    protected $table = 'student_attendance'; // Force singular table name

    protected $fillable = [
        'student_id',
        'class_id',
        'section_id',
        'attendance_date',
        'status',
        'check_in_time',
        'check_out_time',
        'attendance_type',
        'biometric_id',
        'temperature',
        'remarks',
        'marked_by'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
        'temperature' => 'decimal:1'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    public function markedBy()
    {
        return $this->belongsTo(Admin::class, 'marked_by');
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'Present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }

    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    public function scopeBySection($query, $sectionId)
    {
        return $query->where('section_id', $sectionId);
    }
}
