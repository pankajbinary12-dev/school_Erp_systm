<?php

namespace App\Services;

use App\Models\BiometricDevice;
use App\Models\StaffMember;
use App\Models\StaffAttendance;
use App\Models\AttendanceLocationLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class BiometricService
{
    /**
     * Process biometric attendance from device
     */
    public function processBiometricAttendance($deviceCode, $biometricData)
    {
        // Find device
        $device = BiometricDevice::where('device_code', $deviceCode)
            ->where('status', 'Active')
            ->first();

        if (!$device) {
            return [
                'success' => false,
                'message' => 'Device not found or inactive',
                'code' => 'DEVICE_NOT_FOUND'
            ];
        }

        // Find staff by employee_id or biometric_id
        $staff = StaffMember::where('employee_id', $biometricData['employee_id'])
            ->orWhere('email', $biometricData['employee_id']) // fallback
            ->first();

        if (!$staff) {
            return [
                'success' => false,
                'message' => 'Staff member not found',
                'code' => 'STAFF_NOT_FOUND'
            ];
        }

        // Determine action (check-in or check-out)
        $today = now()->format('Y-m-d');
        $existingAttendance = StaffAttendance::where('staff_id', $staff->id)
            ->where('attendance_date', $today)
            ->first();

        $action = (!$existingAttendance || !$existingAttendance->check_in) ? 'check_in' : 'check_out';
        $currentTime = now()->format('H:i:s');

        try {
            if ($action === 'check_in') {
                // Check-in
                $isLate = $currentTime > '09:00:00';

                $attendance = StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $staff->id,
                        'attendance_date' => $today
                    ],
                    [
                        'status' => $isLate ? 'Late' : 'Present',
                        'check_in' => $currentTime,
                        'expected_check_in' => '09:00:00',
                        'is_late' => $isLate,
                        'remarks' => 'Biometric check-in'
                    ]
                );

                // Log biometric location
                if ($device->latitude && $device->longitude) {
                    AttendanceLocationLog::create([
                        'attendance_id' => $attendance->id,
                        'school_id' => $device->school_id,
                        'latitude' => $device->latitude,
                        'longitude' => $device->longitude,
                        'accuracy' => 0, // Biometric device has fixed location
                        'distance_from_school' => 0, // Device is at school
                        'device_type' => 'biometric',
                        'device_id' => $device->device_code,
                        'location_verified' => true,
                        'within_geofence' => true,
                        'verification_method' => 'Biometric',
                        'verification_notes' => "Device: {$device->device_name} at {$device->location_description}",
                        'ai_approved' => true,
                        'confidence_score' => 100 // Biometric is 100% confident
                    ]);
                }

                // Update device sync time
                $device->update(['last_sync_at' => now()]);

                return [
                    'success' => true,
                    'message' => 'Check-in successful',
                    'action' => 'check_in',
                    'data' => [
                        'staff_name' => $staff->full_name,
                        'employee_id' => $staff->employee_id,
                        'time' => $currentTime,
                        'status' => $isLate ? 'Late' : 'Present',
                        'device' => $device->device_name
                    ]
                ];

            } else {
                // Check-out
                $existingAttendance->check_out = $currentTime;
                $existingAttendance->save();

                // Log biometric location for check-out
                if ($device->latitude && $device->longitude) {
                    AttendanceLocationLog::create([
                        'attendance_id' => $existingAttendance->id,
                        'school_id' => $device->school_id,
                        'latitude' => $device->latitude,
                        'longitude' => $device->longitude,
                        'accuracy' => 0,
                        'distance_from_school' => 0,
                        'device_type' => 'biometric',
                        'device_id' => $device->device_code,
                        'location_verified' => true,
                        'within_geofence' => true,
                        'verification_method' => 'Biometric',
                        'verification_notes' => "Check-out via {$device->device_name}",
                        'ai_approved' => true,
                        'confidence_score' => 100
                    ]);
                }

                $device->update(['last_sync_at' => now()]);

                return [
                    'success' => true,
                    'message' => 'Check-out successful',
                    'action' => 'check_out',
                    'data' => [
                        'staff_name' => $staff->full_name,
                        'employee_id' => $staff->employee_id,
                        'time' => $currentTime,
                        'working_hours' => $existingAttendance->working_hours,
                        'device' => $device->device_name
                    ]
                ];
            }
        } catch (\Exception $e) {
            Log::error('Biometric attendance error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Error processing attendance: ' . $e->getMessage(),
                'code' => 'PROCESSING_ERROR'
            ];
        }
    }

    /**
     * Sync with biometric device (pull attendance data)
     */
    public function syncWithDevice($deviceId)
    {
        $device = BiometricDevice::find($deviceId);

        if (!$device || !$device->api_endpoint) {
            return [
                'success' => false,
                'message' => 'Device not configured properly'
            ];
        }

        try {
            // Call device API to get attendance records
            $response = Http::timeout(10)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $device->api_key,
                    'Accept' => 'application/json'
                ])
                ->get($device->api_endpoint . '/attendance/recent');

            if ($response->successful()) {
                $records = $response->json('data', []);
                $processed = 0;

                foreach ($records as $record) {
                    $result = $this->processBiometricAttendance(
                        $device->device_code,
                        $record
                    );

                    if ($result['success']) {
                        $processed++;
                    }
                }

                $device->update(['last_sync_at' => now()]);

                return [
                    'success' => true,
                    'message' => "Synced {$processed} records",
                    'processed' => $processed
                ];
            }

            return [
                'success' => false,
                'message' => 'Device API returned error: ' . $response->status()
            ];

        } catch (\Exception $e) {
            Log::error('Device sync error: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'Sync failed: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Test device connection
     */
    public function testDeviceConnection($deviceId)
    {
        $device = BiometricDevice::find($deviceId);

        if (!$device) {
            return ['success' => false, 'message' => 'Device not found'];
        }

        try {
            $response = Http::timeout(5)
                ->withHeaders([
                    'Authorization' => 'Bearer ' . $device->api_key
                ])
                ->get($device->api_endpoint . '/ping');

            if ($response->successful()) {
                return [
                    'success' => true,
                    'message' => 'Device is online and responding',
                    'device_info' => $response->json()
                ];
            }

            return [
                'success' => false,
                'message' => 'Device not responding'
            ];

        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Connection failed: ' . $e->getMessage()
            ];
        }
    }
}
