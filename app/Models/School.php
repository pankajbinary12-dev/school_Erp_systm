<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class School extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'school_code',
        'school_name',
        'address',
        'city',
        'state',
        'country',
        'pin_code',
        'latitude',
        'longitude',
        'geofence_radius',
        'phone',
        'email',
        'principal_name',
        'geofencing_enabled',
        'strict_mode',
        'office_start_time',
        'office_end_time',
        'status'
    ];

    protected $casts = [
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8',
        'geofencing_enabled' => 'boolean',
        'strict_mode' => 'boolean',
    ];

    // Relationships
    public function staffMembers()
    {
        return $this->hasMany(StaffMember::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function biometricDevices()
    {
        return $this->hasMany(BiometricDevice::class);
    }

    public function attendanceLogs()
    {
        return $this->hasMany(AttendanceLocationLog::class);
    }

    public function violations()
    {
        return $this->hasMany(GeofenceViolation::class);
    }

    // Calculate distance from a point (Haversine formula)
    public function distanceFrom($latitude, $longitude)
    {
        $earthRadius = 6371000; // meters

        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $a = sin($latDelta / 2) * sin($latDelta / 2) +
             cos($latFrom) * cos($latTo) *
             sin($lonDelta / 2) * sin($lonDelta / 2);
        
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c; // Distance in meters
    }

    // Check if location is within geofence
    public function isWithinGeofence($latitude, $longitude)
    {
        $distance = $this->distanceFrom($latitude, $longitude);
        return $distance <= $this->geofence_radius;
    }

    // Get geofence boundary coordinates (for map display)
    public function getGeofenceBoundary()
    {
        $radius = $this->geofence_radius;
        $lat = $this->latitude;
        $lon = $this->longitude;

        // Calculate boundary points (circle)
        $points = [];
        for ($i = 0; $i <= 360; $i += 10) {
            $angle = deg2rad($i);
            $dx = $radius * cos($angle);
            $dy = $radius * sin($angle);
            
            $deltaLat = $dy / 111320;
            $deltaLon = $dx / (111320 * cos(deg2rad($lat)));
            
            $points[] = [
                'lat' => $lat + $deltaLat,
                'lng' => $lon + $deltaLon
            ];
        }
        
        return $points;
    }
}
