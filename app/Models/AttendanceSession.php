<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceSession extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'teacher_id',
        'class_id',
        'section_id',
        'subject_id',
        'attendance_date',
        'start_time',
        'end_time',
        'status',
        'total_students',
        'present_count',
        'absent_count',
        'late_count',
        'leave_count',
        'notes'
    ];

    protected $casts = [
        'attendance_date' => 'date',
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

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'session_id');
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

    public function scopeToday($query)
    {
        return $query->whereDate('attendance_date', today());
    }

    // Methods
    public function updateCounts()
    {
        $this->present_count = $this->attendances()->where('status', 'Present')->count();
        $this->absent_count = $this->attendances()->where('status', 'Absent')->count();
        $this->late_count = $this->attendances()->where('status', 'Late')->count();
        $this->leave_count = $this->attendances()->where('status', 'Leave')->count();
        $this->save();
    }

    public function getAttendancePercentageAttribute()
    {
        if ($this->total_students == 0) return 0;
        return round(($this->present_count / $this->total_students) * 100, 2);
    }
}
