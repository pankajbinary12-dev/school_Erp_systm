<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

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
        'remarks'
    ];

    protected $casts = [
        'attendance_date' => 'date',
    ];

    // Relationships
    public function staff()
    {
        return $this->belongsTo(StaffMember::class, 'staff_id');
    }
}
