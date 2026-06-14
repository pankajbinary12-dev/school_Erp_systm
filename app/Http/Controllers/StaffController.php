<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffMember;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    // List all staff members
    public function index()
    {
        return view('admin.staff.all');
    }

    // Get staff data for DataTables
    public function getStaffData(Request $request)
    {
        $staff = StaffMember::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    // Show add staff form
    public function create()
    {
        return view('admin.staff.add');
    }

    // Store new staff member
    public function store(Request $request)
    {
        $request->validate([
            'employee_id' => 'required|unique:staff_members,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_members,email',
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:255',
            'department' => 'nullable|string|max:255',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'photo' => 'nullable|image|max:2048'
        ]);

        try {
            $data = $request->except('photo');
            
            if ($request->hasFile('photo')) {
                $data['photo'] = $request->file('photo')->store('staff_photos', 'public');
            }

            $staff = StaffMember::create($data);

            return response()->json([
                'success' => true,
                'message' => 'Staff member added successfully',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show edit staff form
    public function edit($id)
    {
        $staff = StaffMember::findOrFail($id);
        
        // Check if it's an AJAX request
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $staff
            ]);
        }
        
        return view('admin.staff.edit', compact('staff'));
    }

    // Update staff member
    public function update(Request $request, $id)
    {
        $staff = StaffMember::findOrFail($id);

        $request->validate([
            'employee_id' => 'required|unique:staff_members,employee_id,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_members,email,' . $id,
            'phone' => 'required|string|max:20',
            'designation' => 'required|string|max:255',
            'photo' => 'nullable|image|max:2048'
        ]);

        try {
            $data = $request->except('photo');
            
            if ($request->hasFile('photo')) {
                // Delete old photo
                if ($staff->photo) {
                    Storage::disk('public')->delete($staff->photo);
                }
                $data['photo'] = $request->file('photo')->store('staff_photos', 'public');
            }

            $staff->update($data);

            return response()->json([
                'success' => true,
                'message' => 'Staff member updated successfully',
                'data' => $staff
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    // Delete staff member (soft delete)
    public function destroy($id)
    {
        try {
            $staff = StaffMember::findOrFail($id);
            $staff->delete();

            return response()->json([
                'success' => true,
                'message' => 'Staff member deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    // Restore deleted staff member
    public function restore($id)
    {
        try {
            $staff = StaffMember::withTrashed()->findOrFail($id);
            $staff->restore();

            return response()->json([
                'success' => true,
                'message' => 'Staff member restored successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error restoring staff member: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get attendance data
    public function getAttendanceData(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $attendance = StaffAttendance::with('staff')
            ->where('attendance_date', $date)
            ->get();

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    // Leave management
    public function leave()
    {
        return view('admin.staff.leave');
    }

    public function getLeaveData(Request $request)
    {
        $leaves = StaffLeave::with('staff')->orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $leaves
        ]);
    }

    public function applyLeave(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'leave_type' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'reason' => 'required|string'
        ]);

        try {
            $leave = StaffLeave::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Leave application submitted successfully',
                'data' => $leave
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error submitting leave: ' . $e->getMessage()
            ], 500);
        }
    }

    public function updateLeaveStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:Approved,Rejected,Pending'
        ]);

        try {
            $leave = StaffLeave::findOrFail($id);
            $leave->update(['status' => $request->status]);

            return response()->json([
                'success' => true,
                'message' => 'Leave status updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating leave status: ' . $e->getMessage()
            ], 500);
        }
    }

    // Show staff attendance page
    public function attendance()
    {
        $staff = StaffMember::where('status', 'Active')
            ->orderBy('first_name')
            ->get();
        
        return view('admin.attendance.staff', compact('staff'));
    }

    // Mark daily attendance
    public function markAttendance(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Half Day,Late,On Leave',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            $attendance = StaffAttendance::updateOrCreate(
                [
                    'staff_id' => $request->staff_id,
                    'attendance_date' => $request->attendance_date
                ],
                [
                    'status' => $request->status,
                    'check_in' => $request->check_in,
                    'check_out' => $request->check_out,
                    'remarks' => $request->remarks,
                    'marked_by' => auth()->guard('admin')->id()
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Attendance marked successfully',
                'data' => $attendance
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error marking attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get attendance for a specific date
    public function getAttendanceByDate(Request $request)
    {
        $date = $request->input('date', now()->format('Y-m-d'));
        
        $staff = StaffMember::where('status', 'Active')
            ->with(['attendances' => function($query) use ($date) {
                $query->where('attendance_date', $date);
            }])
            ->orderBy('first_name')
            ->get();

        return response()->json([
            'success' => true,
            'data' => $staff
        ]);
    }

    // Monthly report view
    public function monthlyReport(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        
        $staff = StaffMember::where('status', 'Active')
            ->orderBy('first_name')
            ->get();

        return view('admin.attendance.staff-monthly-report', compact('staff', 'year', 'month'));
    }

    // Get monthly report data
    public function getMonthlyReportData(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2020|max:2100',
            'month' => 'required|integer|min:1|max:12',
            'staff_id' => 'nullable|exists:staff_members,id'
        ]);

        $year = $request->year;
        $month = $request->month;
        $staffId = $request->staff_id;

        try {
            // Build query
            $query = StaffMember::where('status', 'Active');
            
            if ($staffId) {
                $query->where('id', $staffId);
            }

            $staff = $query->orderBy('first_name')->get();

            $reportData = [];

            foreach ($staff as $member) {
                $summary = $member->getMonthlyAttendanceSummary($year, $month);
                
                // Get daily attendance details
                $dailyAttendance = StaffAttendance::where('staff_id', $member->id)
                    ->forMonth($year, $month)
                    ->orderBy('attendance_date')
                    ->get();

                $reportData[] = [
                    'staff_id' => $member->id,
                    'employee_id' => $member->employee_id,
                    'name' => $member->full_name,
                    'designation' => $member->designation,
                    'department' => $member->department,
                    'summary' => $summary,
                    'daily_records' => $dailyAttendance
                ];
            }

            return response()->json([
                'success' => true,
                'data' => $reportData,
                'month_name' => Carbon::create($year, $month)->format('F Y')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error generating report: ' . $e->getMessage()
            ], 500);
        }
    }

    // Export monthly report (PDF/Excel)
    public function exportMonthlyReport(Request $request)
    {
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);
        $format = $request->input('format', 'pdf'); // pdf or excel

        // This is a placeholder - you can implement actual export logic
        return response()->json([
            'success' => true,
            'message' => 'Export functionality - implement with Laravel Excel or DomPDF'
        ]);
    }

    // Admin manually marks attendance for any staff
    public function adminMarkAttendance(Request $request)
    {
        $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'attendance_date' => 'required|date',
            'action' => 'required|in:check_in,check_out',
            'time' => 'required|date_format:H:i',
            'status' => 'nullable|in:Present,Absent,Late,Half Day,On Leave',
            'remarks' => 'nullable|string|max:500'
        ]);

        try {
            $staff = StaffMember::find($request->staff_id);
            $date = $request->attendance_date;
            $action = $request->action;
            $time = $request->time . ':00';

            if ($action === 'check_in') {
                // Check if already checked in
                $existing = StaffAttendance::where('staff_id', $staff->id)
                    ->where('attendance_date', $date)
                    ->first();

                if ($existing && $existing->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already checked in for this date'
                    ], 400);
                }

                $isLate = $time > '09:00:00';
                $status = $request->status ?? ($isLate ? 'Late' : 'Present');

                $attendance = StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $staff->id,
                        'attendance_date' => $date
                    ],
                    [
                        'status' => $status,
                        'check_in' => $time,
                        'expected_check_in' => '09:00:00',
                        'is_late' => $isLate,
                        'remarks' => ($request->remarks ?? '') . ' [Admin marked]',
                        'marked_by' => auth()->guard('admin')->id()
                    ]
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in marked successfully by admin',
                    'data' => $attendance
                ]);

            } else {
                // Check-out
                $attendance = StaffAttendance::where('staff_id', $staff->id)
                    ->where('attendance_date', $date)
                    ->first();

                if (!$attendance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No check-in found for this date'
                    ], 400);
                }

                if ($attendance->check_out) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Already checked out for this date'
                    ], 400);
                }

                $attendance->check_out = $time;
                $attendance->save();

                return response()->json([
                    'success' => true,
                    'message' => 'Check-out marked successfully by admin',
                    'data' => $attendance
                ]);
            }
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error: ' . $e->getMessage()
            ], 500);
        }
    }

    // Process biometric attendance
    public function processBiometric(Request $request)
    {
        $request->validate([
            'device_code' => 'required|string',
            'employee_id' => 'required|string',
            'timestamp' => 'nullable|date'
        ]);

        $biometricService = new \App\Services\BiometricService();
        
        $result = $biometricService->processBiometricAttendance(
            $request->device_code,
            [
                'employee_id' => $request->employee_id,
                'timestamp' => $request->timestamp ?? now()
            ]
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json($result, 400);
    }

    // Sync with biometric device
    public function syncBiometricDevice(Request $request, $deviceId)
    {
        $biometricService = new \App\Services\BiometricService();
        $result = $biometricService->syncWithDevice($deviceId);

        return response()->json($result);
    }

    // Test biometric device connection
    public function testBiometricDevice($deviceId)
    {
        $biometricService = new \App\Services\BiometricService();
        $result = $biometricService->testDeviceConnection($deviceId);

        return response()->json($result);
    }

    // Bulk mark attendance
    public function bulkMarkAttendance(Request $request)
    {
        $request->validate([
            'attendance_date' => 'required|date',
            'attendance_records' => 'required|array',
            'attendance_records.*.staff_id' => 'required|exists:staff_members,id',
            'attendance_records.*.status' => 'required|in:Present,Absent,Half Day,Late,On Leave',
        ]);

        try {
            DB::beginTransaction();

            $saved = 0;
            foreach ($request->attendance_records as $record) {
                StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $record['staff_id'],
                        'attendance_date' => $request->attendance_date
                    ],
                    [
                        'status' => $record['status'],
                        'check_in' => $record['check_in'] ?? null,
                        'check_out' => $record['check_out'] ?? null,
                        'remarks' => $record['remarks'] ?? null,
                        'marked_by' => auth()->guard('admin')->id()
                    ]
                );
                $saved++;
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Attendance marked for {$saved} staff members"
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error in bulk attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    // Biometric Device Management
    public function biometricDevices()
    {
        return view('admin.attendance.biometric-devices');
    }

    public function biometricTestSimulator()
    {
        return view('admin.attendance.biometric-test');
    }

    public function listBiometricDevices()
    {
        $devices = \App\Models\BiometricDevice::orderBy('created_at', 'desc')->get();
        
        return response()->json([
            'success' => true,
            'data' => $devices
        ]);
    }

    public function storeBiometricDevice(Request $request)
    {
        $request->validate([
            'device_code' => 'required|unique:biometric_devices,device_code',
            'device_name' => 'required|string|max:255',
            'device_type' => 'required|in:fingerprint,face,card,hybrid',
            'school_id' => 'nullable|exists:schools,id',
            'location_description' => 'nullable|string|max:500',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric',
            'ip_address' => 'nullable|ip',
            'port' => 'nullable|integer',
            'api_endpoint' => 'nullable|url',
            'api_key' => 'nullable|string',
            'status' => 'required|in:Active,Inactive,Maintenance'
        ]);

        try {
            $device = \App\Models\BiometricDevice::create($request->all());

            return response()->json([
                'success' => true,
                'message' => 'Biometric device added successfully',
                'data' => $device
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error adding device: ' . $e->getMessage()
            ], 500);
        }
    }

    public function deleteBiometricDevice($id)
    {
        try {
            $device = \App\Models\BiometricDevice::findOrFail($id);
            $device->delete();

            return response()->json([
                'success' => true,
                'message' => 'Device deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting device: ' . $e->getMessage()
            ], 500);
        }
    }
}
