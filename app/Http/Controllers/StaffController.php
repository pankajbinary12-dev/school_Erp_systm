<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\StaffMember;
use App\Models\StaffAttendance;
use App\Models\StaffLeave;
use Illuminate\Support\Facades\Storage;

class StaffController extends Controller
{
    // All Staff
    public function index()
    {
        return view('admin.staff.all');
    }

    public function getStaffData()
    {
        try {
            // Get only non-deleted staff
            $staff = StaffMember::orderBy('employee_id')->get();
            return response()->json(['data' => $staff]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Get Trashed Staff
    public function getTrashedStaff()
    {
        try {
            $staff = StaffMember::onlyTrashed()->orderBy('deleted_at', 'desc')->get();
            return response()->json(['data' => $staff]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    // Add Staff
    public function create()
    {
        return view('admin.staff.add');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|string|unique:staff_members,employee_id',
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_members,email',
            'phone' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'qualification' => 'nullable|string',
            'designation' => 'required|string',
            'department' => 'nullable|string',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'photo' => 'nullable|image|max:5120',
            'status' => 'nullable|in:Active,Inactive,On Leave',
        ]);

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('staff', 'public');
        }

        $validated['status'] = $request->input('status', 'Active');

        $staff = StaffMember::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff member added successfully!',
            'data' => $staff
        ]);
    }

    public function edit($id)
    {
        $staff = StaffMember::findOrFail($id);
        return response()->json(['success' => true, 'data' => $staff]);
    }

    public function update(Request $request, $id)
    {
        $staff = StaffMember::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|string|unique:staff_members,employee_id,' . $id,
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|unique:staff_members,email,' . $id,
            'phone' => 'required|string|max:15',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'state' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
            'qualification' => 'nullable|string',
            'designation' => 'required|string',
            'department' => 'nullable|string',
            'joining_date' => 'required|date',
            'salary' => 'nullable|numeric',
            'photo' => 'nullable|image|max:5120',
            'status' => 'nullable|in:Active,Inactive,On Leave',
        ]);

        if ($request->hasFile('photo')) {
            if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
                Storage::disk('public')->delete($staff->photo);
            }
            $validated['photo'] = $request->file('photo')->store('staff', 'public');
        }

        $staff->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Staff member updated successfully!',
            'data' => $staff
        ]);
    }

    public function destroy($id)
    {
        $staff = StaffMember::findOrFail($id);
        $staff->delete(); // Soft delete

        return response()->json([
            'success' => true,
            'message' => 'Staff member deleted successfully! You can restore it later.'
        ]);
    }

    public function restore($id)
    {
        $staff = StaffMember::onlyTrashed()->findOrFail($id);
        $staff->restore();

        return response()->json([
            'success' => true,
            'message' => 'Staff member restored successfully!'
        ]);
    }

    public function forceDelete($id)
    {
        $staff = StaffMember::onlyTrashed()->findOrFail($id);
        
        // Delete photo if exists
        if ($staff->photo && Storage::disk('public')->exists($staff->photo)) {
            Storage::disk('public')->delete($staff->photo);
        }
        
        $staff->forceDelete(); // Permanent delete

        return response()->json([
            'success' => true,
            'message' => 'Staff member permanently deleted!'
        ]);
    }

    // Attendance
    public function attendance()
    {
        return view('admin.staff.attendance');
    }

    public function getAttendanceData(Request $request)
    {
        $date = $request->input('date', date('Y-m-d'));
        
        $attendance = StaffAttendance::with('staff')
            ->whereDate('attendance_date', $date)
            ->get();

        return response()->json(['data' => $attendance]);
    }

    public function markAttendance(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'attendance_date' => 'required|date',
            'status' => 'required|in:Present,Absent,Half Day,Late,On Leave',
            'check_in' => 'nullable|date_format:H:i',
            'check_out' => 'nullable|date_format:H:i',
            'remarks' => 'nullable|string',
        ]);

        $attendance = StaffAttendance::updateOrCreate(
            [
                'staff_id' => $validated['staff_id'],
                'attendance_date' => $validated['attendance_date']
            ],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully!',
            'data' => $attendance
        ]);
    }

    // Leave
    public function leave()
    {
        return view('admin.staff.leave');
    }

    public function getLeaveData()
    {
        $leaves = StaffLeave::with('staff')->orderBy('created_at', 'desc')->get();
        return response()->json(['data' => $leaves]);
    }

    public function applyLeave(Request $request)
    {
        
        $validated = $request->validate([
            'staff_id' => 'required|exists:staff_members,id',
            'leave_type' => 'required|in:Sick Leave,Casual Leave,Earned Leave,Maternity Leave,Other',
            'from_date' => 'required|date',
            'to_date' => 'required|date|after_or_equal:from_date',
            'reason' => 'required|string',
        ]);

        // Calculate total days
        $from = new \DateTime($validated['from_date']);
        $to = new \DateTime($validated['to_date']);
        $validated['total_days'] = $to->diff($from)->days + 1;
        $validated['status'] = 'Pending'; // Default status

        $leave = StaffLeave::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Leave application submitted successfully!',
            'data' => $leave
        ]);
    }

    public function updateLeaveStatus(Request $request, $id)
    {
        $leave = StaffLeave::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:Pending,Approved,Rejected',
            'admin_remarks' => 'nullable|string',
        ]);

        $leave->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Leave status updated successfully!',
            'data' => $leave
        ]);
    }
}
