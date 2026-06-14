<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceLocationLog extends Model
{
    protected $fillable = [
        'attendance_id',
        'school_id',
        'latitude',
        'longitude',
        'accuracy',
        'distance_from_school',
        'device_type',
        'device_id',
        'ip_address',
        'user_agent',
        'location_verified',
        'within_geofence',
        'verification_method',
        'verification_notes',
        'ai_analysis',
        'confidence_score',
        'ai_approved',
        'ai_flags'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'accuracy' => 'decimal:2',
        'distance_from_school' => 'decimal:2',
        'location_verified' => 'boolean',
        'within_geofence' => 'boolean',
        'ai_analysis' => 'array',
        'confidence_score' => 'decimal:2',
        'ai_approved' => 'boolean',
    ];

    public function attendance()
    {
        return $this->belongsTo(StaffAttendance::class, 'attendance_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
