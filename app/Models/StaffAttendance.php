<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class StaffAttendance extends Model
{
    use SoftDeletes;

    protected $table = 'staff_attendance';

    protected $fillable = [
        'staff_id',
        'attendance_date',
        'status',
        'check_in',
        'check_out',
        'remarks',
        'working_hours',
        'marked_by',
        'expected_check_in',
        'is_late'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'is_late' => 'boolean',
        'working_hours' => 'decimal:2'
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(StaffMember::class, 'staff_id');
    }

    public function markedBy()
    {
        return $this->belongsTo(Admin::class, 'marked_by');
    }

    // Scopes for filtering
    public function scopeForMonth($query, $year, $month)
    {
        return $query->whereYear('attendance_date', $year)
                     ->whereMonth('attendance_date', $month);
    }

    public function scopeForStaff($query, $staffId)
    {
        return $query->where('staff_id', $staffId);
    }

    public function scopePresent($query)
    {
        return $query->where('status', 'Present');
    }

    public function scopeAbsent($query)
    {
        return $query->where('status', 'Absent');
    }

    public function scopeLate($query)
    {
        return $query->where('is_late', true);
    }

    // Calculate working hours automatically
    public function calculateWorkingHours()
    {
        if ($this->check_in && $this->check_out) {
            $checkIn = Carbon::parse($this->check_in);
            $checkOut = Carbon::parse($this->check_out);
            $this->working_hours = $checkOut->diffInHours($checkIn, true);
        }
    }

    // Check if late
    public function checkIfLate()
    {
        if ($this->check_in && $this->expected_check_in) {
            $checkIn = Carbon::parse($this->check_in);
            $expected = Carbon::parse($this->expected_check_in);
            $this->is_late = $checkIn->greaterThan($expected);
        }
    }

    // Boot method to auto-calculate
    protected static function boot()
    {
        parent::boot();

        static::saving(function ($attendance) {
            $attendance->calculateWorkingHours();
            $attendance->checkIfLate();
        });
    }
}
