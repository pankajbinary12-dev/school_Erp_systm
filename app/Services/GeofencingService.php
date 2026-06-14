<?php

namespace App\Services;

use App\Models\School;
use App\Models\AttendanceLocationLog;
use App\Models\GeofenceViolation;
use App\Models\StaffAttendance;
use Illuminate\Support\Facades\Log;

class GeofencingService
{
    /**
     * Verify location and create attendance with AI validation
     */
    public function verifyAndMarkAttendance($staffMember, $locationData, $attendanceData)
    {
        $school = School::find($staffMember->school_id);
        
        if (!$school) {
            return [
                'success' => false,
                'message' => 'School not found for this staff member.',
                'code' => 'SCHOOL_NOT_FOUND'
            ];
        }

        // Check if geofencing is enabled
        if (!$school->geofencing_enabled) {
            return $this->markAttendanceWithoutGeofence($staffMember, $attendanceData);
        }

        // Validate location data
        if (!isset($locationData['latitude']) || !isset($locationData['longitude'])) {
            if ($school->strict_mode) {
                return [
                    'success' => false,
                    'message' => 'Location data is required. Please enable GPS.',
                    'code' => 'LOCATION_REQUIRED'
                ];
            }
            return $this->markAttendanceWithoutGeofence($staffMember, $attendanceData);
        }

        // Calculate distance from school
        $distance = $school->distanceFrom(
            $locationData['latitude'],
            $locationData['longitude']
        );

        $withinGeofence = $distance <= $school->geofence_radius;

        // AI-powered verification
        $aiVerification = $this->performAIVerification([
            'school' => $school,
            'staff' => $staffMember,
            'location' => $locationData,
            'distance' => $distance,
            'within_geofence' => $withinGeofence
        ]);

        // If outside geofence or AI flags suspicious activity
        if (!$withinGeofence || !$aiVerification['approved']) {
            $this->logViolation($staffMember, $school, $locationData, $distance, $aiVerification);
            
            if ($school->strict_mode) {
                return [
                    'success' => false,
                    'message' => $aiVerification['message'] ?? 'You are outside the school premises. Distance: ' . round($distance) . 'm',
                    'code' => 'OUTSIDE_GEOFENCE',
                    'data' => [
                        'distance' => round($distance, 2),
                        'allowed_radius' => $school->geofence_radius,
                        'ai_flags' => $aiVerification['flags'] ?? []
                    ]
                ];
            }
        }

        // Mark attendance
        $attendance = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $staffMember->id,
                'attendance_date' => $attendanceData['attendance_date']
            ],
            array_merge($attendanceData, [
                'remarks' => ($attendanceData['remarks'] ?? '') . 
                           ($withinGeofence ? '' : ' [Outside Geofence]')
            ])
        );

        // Log location data
        $locationLog = AttendanceLocationLog::create([
            'attendance_id' => $attendance->id,
            'school_id' => $school->id,
            'latitude' => $locationData['latitude'],
            'longitude' => $locationData['longitude'],
            'accuracy' => $locationData['accuracy'] ?? null,
            'distance_from_school' => $distance,
            'device_type' => $locationData['device_type'] ?? 'web',
            'device_id' => $locationData['device_id'] ?? null,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'location_verified' => true,
            'within_geofence' => $withinGeofence,
            'verification_method' => 'GPS',
            'verification_notes' => $aiVerification['notes'] ?? null,
            'ai_analysis' => $aiVerification['analysis'] ?? null,
            'confidence_score' => $aiVerification['confidence'] ?? 100,
            'ai_approved' => $aiVerification['approved'],
            'ai_flags' => $aiVerification['flags'] ?? null
        ]);

        return [
            'success' => true,
            'message' => 'Attendance marked successfully.',
            'data' => [
                'attendance' => $attendance,
                'location_log' => $locationLog,
                'distance' => round($distance, 2),
                'within_geofence' => $withinGeofence,
                'ai_confidence' => $aiVerification['confidence'] ?? 100
            ]
        ];
    }

    /**
     * AI-powered location verification
     */
    private function performAIVerification($data)
    {
        $flags = [];
        $confidence = 100;
        $approved = true;
        $analysis = [];

        // Check 1: Distance verification
        if ($data['distance'] > $data['school']->geofence_radius) {
            $flags[] = 'Outside geofence radius';
            $confidence -= 30;
            $analysis['distance_check'] = 'failed';
        } else {
            $analysis['distance_check'] = 'passed';
        }

        // Check 2: GPS accuracy
        if (isset($data['location']['accuracy']) && $data['location']['accuracy'] > 50) {
            $flags[] = 'Low GPS accuracy';
            $confidence -= 10;
            $analysis['accuracy_check'] = 'warning';
        } else {
            $analysis['accuracy_check'] = 'passed';
        }

        // Check 3: Time-based verification
        $currentTime = now()->format('H:i:s');
        $officeStart = $data['school']->office_start_time;
        $officeEnd = $data['school']->office_end_time;
        
        if ($currentTime < $officeStart || $currentTime > $officeEnd) {
            $flags[] = 'Outside office hours';
            $confidence -= 5;
            $analysis['time_check'] = 'warning';
        } else {
            $analysis['time_check'] = 'passed';
        }

        // Check 4: Suspicious pattern detection
        $recentAttendance = StaffAttendance::where('staff_id', $data['staff']->id)
            ->where('attendance_date', now()->format('Y-m-d'))
            ->count();
        
        if ($recentAttendance > 0) {
            $flags[] = 'Multiple attendance attempts today';
            $confidence -= 20;
            $analysis['duplicate_check'] = 'failed';
        } else {
            $analysis['duplicate_check'] = 'passed';
        }

        // Check 5: Device consistency
        $previousLogs = AttendanceLocationLog::whereHas('attendance', function($q) use ($data) {
                $q->where('staff_id', $data['staff']->id);
            })
            ->where('device_id', $data['location']['device_id'] ?? 'unknown')
            ->count();
        
        if ($previousLogs > 0) {
            $confidence += 5; // Bonus for consistent device
            $analysis['device_check'] = 'trusted';
        } else {
            $analysis['device_check'] = 'new_device';
        }

        // Final decision
        if ($confidence < 50) {
            $approved = false;
        }

        return [
            'approved' => $approved,
            'confidence' => max(0, min(100, $confidence)),
            'flags' => implode(', ', $flags),
            'analysis' => $analysis,
            'notes' => count($flags) > 0 ? 'AI detected: ' . implode(', ', $flags) : 'All checks passed',
            'message' => !$approved ? 'Attendance verification failed. Please contact admin.' : null
        ];
    }

    /**
     * Log geofence violation
     */
    private function logViolation($staffMember, $school, $locationData, $distance, $aiVerification)
    {
        GeofenceViolation::create([
            'staff_id' => $staffMember->id,
            'school_id' => $school->id,
            'violation_date' => now()->format('Y-m-d'),
            'violation_time' => now()->format('H:i:s'),
            'attempted_latitude' => $locationData['latitude'],
            'attempted_longitude' => $locationData['longitude'],
            'distance_from_school' => $distance,
            'violation_type' => $distance > $school->geofence_radius ? 'Outside Geofence' : 'Suspicious Location',
            'violation_details' => json_encode([
                'ai_flags' => $aiVerification['flags'] ?? [],
                'confidence' => $aiVerification['confidence'] ?? 0,
                'device_info' => [
                    'type' => $locationData['device_type'] ?? 'unknown',
                    'id' => $locationData['device_id'] ?? 'unknown',
                    'ip' => request()->ip()
                ]
            ]),
            'admin_notified' => false,
            'resolved' => false
        ]);

        // Log for admin notification
        Log::warning('Geofence violation detected', [
            'staff_id' => $staffMember->id,
            'school_id' => $school->id,
            'distance' => $distance,
            'ai_confidence' => $aiVerification['confidence'] ?? 0
        ]);
    }

    /**
     * Mark attendance without geofence (fallback)
     */
    private function markAttendanceWithoutGeofence($staffMember, $attendanceData)
    {
        $attendance = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $staffMember->id,
                'attendance_date' => $attendanceData['attendance_date']
            ],
            array_merge($attendanceData, [
                'remarks' => ($attendanceData['remarks'] ?? '') . ' [No Location Data]'
            ])
        );

        return [
            'success' => true,
            'message' => 'Attendance marked without location verification.',
            'data' => [
                'attendance' => $attendance,
                'location_verified' => false
            ]
        ];
    }

    /**
     * Get school location for map display
     */
    public function getSchoolLocationData($schoolId)
    {
        $school = School::find($schoolId);
        
        if (!$school) {
            return null;
        }

        return [
            'school' => [
                'id' => $school->id,
                'name' => $school->school_name,
                'latitude' => $school->latitude,
                'longitude' => $school->longitude,
                'radius' => $school->geofence_radius,
                'address' => $school->address
            ],
            'geofence_boundary' => $school->getGeofenceBoundary(),
            'biometric_devices' => $school->biometricDevices()
                ->where('status', 'Active')
                ->get()
                ->map(function($device) {
                    return [
                        'name' => $device->device_name,
                        'location' => $device->location_description,
                        'latitude' => $device->latitude,
                        'longitude' => $device->longitude
                    ];
                })
        ];
    }
}
