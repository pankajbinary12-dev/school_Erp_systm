<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeofenceViolation extends Model
{
    protected $fillable = [
        'staff_id',
        'school_id',
        'violation_date',
        'violation_time',
        'attempted_latitude',
        'attempted_longitude',
        'distance_from_school',
        'violation_type',
        'violation_details',
        'admin_notified',
        'resolved',
        'resolution_notes'
    ];

    protected $casts = [
        'violation_date' => 'date',
        'attempted_latitude' => 'decimal:8',
        'attempted_longitude' => 'decimal:8',
        'distance_from_school' => 'decimal:2',
        'admin_notified' => 'boolean',
        'resolved' => 'boolean',
    ];

    public function staff()
    {
        return $this->belongsTo(StaffMember::class, 'staff_id');
    }

    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
