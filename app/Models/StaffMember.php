<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\HasRolesAndPermissions;

class StaffMember extends Model
{
    use HasRolesAndPermissions, SoftDeletes;

    protected $fillable = [
        'employee_id',
        'first_name',
        'last_name',
        'date_of_birth',
        'gender',
        'email',
        'phone',
        'address',
        'city',
        'state',
        'pin_code',
        'qualification',
        'designation',
        'department',
        'joining_date',
        'salary',
        'photo',
        'status'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'joining_date' => 'date',
        'salary' => 'decimal:2'
    ];

    // Roles relationship
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'staff_roles', 'staff_id', 'role_id');
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    // Attendance relationship
    public function attendances()
    {
        return $this->hasMany(StaffAttendance::class, 'staff_id');
    }

    // Get monthly attendance summary
    public function getMonthlyAttendanceSummary($year, $month)
    {
        $attendances = $this->attendances()
            ->forMonth($year, $month)
            ->get();

        return [
            'total_days' => $attendances->count(),
            'present' => $attendances->where('status', 'Present')->count(),
            'absent' => $attendances->where('status', 'Absent')->count(),
            'late' => $attendances->where('is_late', true)->count(),
            'half_day' => $attendances->where('status', 'Half Day')->count(),
            'on_leave' => $attendances->where('status', 'On Leave')->count(),
            'total_working_hours' => $attendances->sum('working_hours'),
            'attendance_percentage' => $attendances->count() > 0 
                ? round(($attendances->whereIn('status', ['Present', 'Half Day', 'Late'])->count() / $attendances->count()) * 100, 2)
                : 0
        ];
    }
}
