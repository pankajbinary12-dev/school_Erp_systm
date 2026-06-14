<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class TeacherController extends Controller
{
    public function dashboard()
    {
        $teacher = auth()->guard('teacher')->user();
        return view('teacher.dashboard', compact('teacher'));
    }

    // Teacher's own attendance view
    public function myAttendance()
    {
        $teacher = auth()->guard('teacher')->user();
        return view('teacher.attendance.my-attendance', compact('teacher'));
    }

    // Get teacher's attendance data
    public function getMyAttendanceData(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        $year = $request->input('year', now()->year);
        $month = $request->input('month', now()->month);

        // Find corresponding staff member by employee_id or email
        $staffMember = \App\Models\StaffMember::where('employee_id', $teacher->employee_id)
            ->orWhere('email', $teacher->email)
            ->first();

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found. Please contact admin.'
            ], 404);
        }

        // Get attendance records
        $attendances = \App\Models\StaffAttendance::where('staff_id', $staffMember->id)
            ->whereYear('attendance_date', $year)
            ->whereMonth('attendance_date', $month)
            ->orderBy('attendance_date', 'desc')
            ->get();

        // Calculate summary
        $summary = [
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

        return response()->json([
            'success' => true,
            'data' => [
                'attendances' => $attendances,
                'summary' => $summary,
                'staff_info' => [
                    'name' => $staffMember->full_name,
                    'employee_id' => $staffMember->employee_id,
                    'designation' => $staffMember->designation,
                    'department' => $staffMember->department
                ]
            ],
            'month_name' => \Carbon\Carbon::create($year, $month)->format('F Y')
        ]);
    }

    // Teacher marks own attendance (Check-in/Check-out) with GPS verification
    public function markMyAttendance(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $request->validate([
            'action' => 'required|in:check_in,check_out',
            'attendance_date' => 'nullable|date',
            'location' => 'nullable|array',
            'location.latitude' => 'nullable|numeric',
            'location.longitude' => 'nullable|numeric',
            'location.accuracy' => 'nullable|numeric',
            'location.device_type' => 'nullable|string',
            'location.device_id' => 'nullable|string',
        ]);

        // Find corresponding staff member
        $staffMember = \App\Models\StaffMember::where('employee_id', $teacher->employee_id)
            ->orWhere('email', $teacher->email)
            ->first();

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found. Please contact admin.'
            ], 404);
        }

        $date = $request->input('attendance_date', now()->format('Y-m-d'));
        $currentTime = now()->format('H:i:s');
        $action = $request->action;
        $locationData = $request->input('location', []);

        try {
            if ($action === 'check_in') {
                // Check if already checked in today
                $existing = \App\Models\StaffAttendance::where('staff_id', $staffMember->id)
                    ->where('attendance_date', $date)
                    ->first();

                if ($existing && $existing->check_in) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already checked in today at ' . $existing->check_in
                    ], 400);
                }

                // Determine if late
                $expectedTime = '09:00:00';
                $isLate = $currentTime > $expectedTime;

                // Prepare attendance data
                $attendanceData = [
                    'status' => $isLate ? 'Late' : 'Present',
                    'check_in' => $currentTime,
                    'expected_check_in' => $expectedTime,
                    'is_late' => $isLate,
                    'remarks' => 'Self check-in',
                    'attendance_date' => $date
                ];

                // Check if GPS data is available
                $hasLocation = !empty($locationData['latitude']) && !empty($locationData['longitude']);
                
                if ($hasLocation && $staffMember->school_id) {
                    // Use GeofencingService for verification
                    $geofencingService = new \App\Services\GeofencingService();
                    $result = $geofencingService->verifyAndMarkAttendance(
                        $staffMember,
                        $locationData,
                        $attendanceData
                    );

                    if (!$result['success']) {
                        // If strict mode, return error
                        $school = \App\Models\School::find($staffMember->school_id);
                        if ($school && $school->strict_mode) {
                            return response()->json($result, 400);
                        }
                        // Otherwise, mark without location (fallback)
                    } else {
                        return response()->json([
                            'success' => true,
                            'message' => 'Check-in successful at ' . now()->format('h:i A'),
                            'data' => $result['data'],
                            'is_late' => $isLate,
                            'location_verified' => true
                        ]);
                    }
                }

                // Fallback: Mark attendance without GPS verification
                $attendance = \App\Models\StaffAttendance::updateOrCreate(
                    [
                        'staff_id' => $staffMember->id,
                        'attendance_date' => $date
                    ],
                    array_merge($attendanceData, [
                        'remarks' => ($attendanceData['remarks'] ?? '') . ' [No GPS]'
                    ])
                );

                return response()->json([
                    'success' => true,
                    'message' => 'Check-in successful at ' . now()->format('h:i A') . ' (GPS not available)',
                    'data' => ['attendance' => $attendance],
                    'is_late' => $isLate,
                    'location_verified' => false
                ]);

            } else { // check_out
                $attendance = \App\Models\StaffAttendance::where('staff_id', $staffMember->id)
                    ->where('attendance_date', $date)
                    ->first();

                if (!$attendance) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Please check-in first before checking out.'
                    ], 400);
                }

                if ($attendance->check_out) {
                    return response()->json([
                        'success' => false,
                        'message' => 'You have already checked out today at ' . $attendance->check_out
                    ], 400);
                }

                // Update check-out
                $attendance->check_out = $currentTime;
                $attendance->save();

                // Try to log location if available
                $hasLocation = !empty($locationData['latitude']) && !empty($locationData['longitude']);
                
                if ($hasLocation && $staffMember->school_id) {
                    $school = \App\Models\School::find($staffMember->school_id);
                    if ($school) {
                        $distance = $school->distanceFrom(
                            $locationData['latitude'],
                            $locationData['longitude']
                        );

                        \App\Models\AttendanceLocationLog::create([
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
                            'within_geofence' => $distance <= $school->geofence_radius,
                            'verification_method' => 'GPS',
                            'ai_approved' => true,
                            'confidence_score' => 100
                        ]);
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Check-out successful at ' . now()->format('h:i A'),
                    'data' => ['attendance' => $attendance],
                    'working_hours' => $attendance->working_hours,
                    'location_verified' => $hasLocation
                ]);
            }
        } catch (\Exception $e) {
            \Log::error('Attendance marking error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error marking attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    // Get today's attendance status
    public function getTodayAttendance()
    {
        $teacher = auth()->guard('teacher')->user();
        
        $staffMember = \App\Models\StaffMember::where('employee_id', $teacher->employee_id)
            ->orWhere('email', $teacher->email)
            ->first();

        if (!$staffMember) {
            return response()->json([
                'success' => false,
                'message' => 'Staff record not found.'
            ], 404);
        }

        $today = now()->format('Y-m-d');
        $attendance = \App\Models\StaffAttendance::where('staff_id', $staffMember->id)
            ->where('attendance_date', $today)
            ->first();

        return response()->json([
            'success' => true,
            'data' => $attendance,
            'has_checked_in' => $attendance && $attendance->check_in ? true : false,
            'has_checked_out' => $attendance && $attendance->check_out ? true : false
        ]);
    }

    public function index()
    {
        return view('admin.teachers.index');
    }

    public function getData(Request $request)
    {
        $query = Teacher::query();

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('employee_id', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $teachers = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_id' => 'required|unique:teachers',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|unique:teachers',
            'phone' => 'required|max:20',
            'qualification' => 'required|max:200',
            'joining_date' => 'required|date',
            'username' => 'required|unique:teachers|max:50',
            'password' => 'required|min:6',
            'photo' => 'nullable|image|max:2048'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'Active';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher = Teacher::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teacher added successfully!',
            'data' => $teacher
        ]);
    }

    public function show($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $teacher
        ]);
    }

    public function update(Request $request, $id)
    {
        $teacher = Teacher::findOrFail($id);

        $validated = $request->validate([
            'employee_id' => 'required|unique:teachers,employee_id,' . $id,
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'email' => 'required|email|unique:teachers,email,' . $id,
            'phone' => 'required|max:20',
            'qualification' => 'required|max:200',
            'joining_date' => 'required|date',
            'username' => 'required|unique:teachers,username,' . $id . '|max:50',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $validated['photo'] = $request->file('photo')->store('teachers', 'public');
        }

        $teacher->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Teacher updated successfully!',
            'data' => $teacher
        ]);
    }

    public function destroy($id)
    {
        $teacher = Teacher::findOrFail($id);
        
        if ($teacher->photo) {
            Storage::disk('public')->delete($teacher->photo);
        }
        
        $teacher->delete();

        return response()->json([
            'success' => true,
            'message' => 'Teacher deleted successfully!'
        ]);
    }

    // My Profile
    public function myProfile()
    {
        $teacher = auth()->guard('teacher')->user();
        
        // Get assigned subjects
        $subjects = \App\Models\Subject::whereHas('teachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();
        
        // Get assigned classes
        $classes = \App\Models\Classes::whereHas('teachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with('sections')->get();
        
        return view('teacher.profile.index', compact('teacher', 'subjects', 'classes'));
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048'
        ]);

        $data = $request->except('photo');
        
        if ($request->hasFile('photo')) {
            if ($teacher->photo) {
                Storage::disk('public')->delete($teacher->photo);
            }
            $data['photo'] = $request->file('photo')->store('teacher_photos', 'public');
        }

        $teacher->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully'
        ]);
    }

    // My Students
    public function myStudents()
    {
        $teacher = auth()->guard('teacher')->user();
        
        // Get classes assigned to teacher
        $classes = \App\Models\Classes::whereHas('teachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with('sections')->get();
        
        return view('teacher.students.index', compact('teacher', 'classes'));
    }

    // Get Students Data
    public function getStudentsData(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');
        
        $query = \App\Models\Student::with(['class', 'section'])
            ->where('class_id', $classId);
        
        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }
        
        $students = $query->orderBy('roll_number')->get();
        
        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    // My Subjects
    public function mySubjects()
    {
        $teacher = auth()->guard('teacher')->user();
        
        $subjects = \App\Models\Subject::whereHas('teachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->with(['classes', 'teachers'])->get();
        
        return view('teacher.subjects.index', compact('teacher', 'subjects'));
    }

    // Assignments List
    public function assignments()
    {
        $teacher = auth()->guard('teacher')->user();
        return view('teacher.assignments.index', compact('teacher'));
    }

    // Get Assignments Data
    public function getAssignmentsData(Request $request)
    {
        try {
            $teacher = auth()->guard('teacher')->user();
            
            $assignments = \App\Models\Assignment::with(['class', 'section', 'subject'])
                ->where('teacher_id', $teacher->id)
                ->orderBy('created_at', 'desc')
                ->get();
            
            // Add computed attributes
            $assignments->each(function($assignment) {
                $assignment->submission_count = $assignment->submissions()->where('status', '!=', 'Pending')->count();
                $assignment->total_students = $assignment->total_students;
                $assignment->submission_percentage = $assignment->submission_percentage;
            });
            
            return response()->json([
                'success' => true,
                'data' => $assignments
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Create Assignment
    public function createAssignment()
    {
        $teacher = auth()->guard('teacher')->user();
        
        // Get teacher's classes and subjects
        $classes = \App\Models\Classes::whereHas('teachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();
        
        $subjects = \App\Models\Subject::whereHas('teachers', function($q) use ($teacher) {
            $q->where('teacher_id', $teacher->id);
        })->get();
        
        return view('teacher.assignments.create', compact('teacher', 'classes', 'subjects'));
    }

    // Store Assignment
    public function storeAssignment(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'assigned_date' => 'required|date',
            'due_date' => 'required|date|after_or_equal:assigned_date',
            'total_marks' => 'required|integer|min:1',
            'instructions' => 'nullable|string',
            'attachment' => 'nullable|file|max:10240'
        ]);

        $data = $request->except('attachment');
        $data['teacher_id'] = $teacher->id;
        
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('assignments', 'public');
        }

        $assignment = \App\Models\Assignment::create($data);
        
        // Create submission records for all students
        $students = \App\Models\Student::where('class_id', $request->class_id);
        if ($request->section_id) {
            $students->where('section_id', $request->section_id);
        }
        
        foreach ($students->get() as $student) {
            \App\Models\AssignmentSubmission::create([
                'assignment_id' => $assignment->id,
                'student_id' => $student->id,
                'status' => 'Pending'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Assignment created successfully',
            'data' => $assignment
        ]);
    }

    // View Assignment
    public function viewAssignment($id)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $assignment = \App\Models\Assignment::with(['class', 'section', 'subject', 'submissions.student'])
            ->where('teacher_id', $teacher->id)
            ->findOrFail($id);
        
        return view('teacher.assignments.view', compact('teacher', 'assignment'));
    }

    // Grade Submission
    public function gradeSubmission(Request $request, $id)
    {
        $request->validate([
            'marks_obtained' => 'required|integer|min:0',
            'teacher_feedback' => 'nullable|string'
        ]);

        $submission = \App\Models\AssignmentSubmission::findOrFail($id);
        
        // Verify teacher owns this assignment
        if ($submission->assignment->teacher_id != auth()->guard('teacher')->id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized'
            ], 403);
        }

        $submission->update([
            'marks_obtained' => $request->marks_obtained,
            'teacher_feedback' => $request->teacher_feedback,
            'status' => 'Graded',
            'graded_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Submission graded successfully',
            'data' => $submission
        ]);
    }

    // Delete Assignment
    public function deleteAssignment($id)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $assignment = \App\Models\Assignment::where('teacher_id', $teacher->id)
            ->findOrFail($id);
        
        $assignment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment deleted successfully'
        ]);
    }

    // Get sections by class ID (for AJAX calls)
    public function getSectionsByClass($classId)
    {
        try {
            $sections = \App\Models\Section::where('class_id', $classId)
                ->where('is_active', 'Active')
                ->orderBy('section_name')
                ->get();

            return response()->json([
                'success' => true,
                'data' => $sections
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error loading sections: ' . $e->getMessage()
            ], 500);
        }
    }

    // ==========================================
    // STUDENT ATTENDANCE MODULE
    // ==========================================

    public function studentAttendance()
    {
        $teacher = auth()->guard('teacher')->user();
        
        // Get teacher's assigned classes
        $assignments = \DB::table('teacher_subjects')
            ->join('classes', 'teacher_subjects.class_id', '=', 'classes.id')
            ->join('sections', 'teacher_subjects.section_id', '=', 'sections.id')
            ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->where('teacher_subjects.teacher_id', $teacher->id)
            ->select(
                'teacher_subjects.class_id',
                'teacher_subjects.section_id',
                'teacher_subjects.subject_id',
                'classes.class_name',
                'sections.section_name',
                'subjects.subject_name'
            )
            ->get()
            ->groupBy('class_id');
        
        return view('teacher.attendance.student-attendance', compact('teacher', 'assignments'));
    }

    public function startAttendanceSession(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id',
            'attendance_date' => 'required|date'
        ]);

        // Check for duplicate session
        $existing = \App\Models\AttendanceSession::where('teacher_id', $teacher->id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->where('attendance_date', $request->attendance_date)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'Attendance session already exists for this date!',
                'session_id' => $existing->id
            ], 400);
        }

        // Get students count
        $studentsCount = \App\Models\Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->count();

        // Create session
        $session = \App\Models\AttendanceSession::create([
            'teacher_id' => $teacher->id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'attendance_date' => $request->attendance_date,
            'start_time' => now()->format('H:i:s'),
            'status' => 'Active',
            'total_students' => $studentsCount
        ]);

        // Create attendance records for all students (default: Absent)
        $students = \App\Models\Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->get();

        foreach ($students as $student) {
            \App\Models\Attendance::create([
                'session_id' => $session->id,
                'student_id' => $student->id,
                'status' => 'Absent'
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attendance session started successfully!',
            'session_id' => $session->id,
            'total_students' => $studentsCount
        ]);
    }

    public function getSessionStudents($sessionId)
    {
        $session = \App\Models\AttendanceSession::with(['class', 'section', 'subject'])->findOrFail($sessionId);
        
        $students = \App\Models\Attendance::where('session_id', $sessionId)
            ->with('student')
            ->get()
            ->map(function($attendance) {
                return [
                    'id' => $attendance->id,
                    'student_id' => $attendance->student_id,
                    'roll_number' => $attendance->student->roll_number ?? 'N/A',
                    'name' => $attendance->student->first_name . ' ' . $attendance->student->last_name,
                    'status' => $attendance->status,
                    'marked_at' => $attendance->marked_at
                ];
            });

        return response()->json([
            'success' => true,
            'session' => $session,
            'students' => $students
        ]);
    }

    public function markStudentAttendance(Request $request)
    {
        $request->validate([
            'attendance_id' => 'required|exists:attendances,id',
            'status' => 'required|in:Present,Absent,Late,Leave'
        ]);

        $attendance = \App\Models\Attendance::findOrFail($request->attendance_id);
        $attendance->update([
            'status' => $request->status,
            'marked_at' => now()->format('H:i:s'),
            'remarks' => $request->remarks
        ]);

        // Update session counts
        $attendance->session->updateCounts();

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully!',
            'attendance' => $attendance,
            'session_counts' => [
                'present' => $attendance->session->present_count,
                'absent' => $attendance->session->absent_count,
                'late' => $attendance->session->late_count,
                'leave' => $attendance->session->leave_count
            ]
        ]);
    }

    public function markAllPresent(Request $request)
    {
        $request->validate([
            'session_id' => 'required|exists:attendance_sessions,id'
        ]);

        $session = \App\Models\AttendanceSession::findOrFail($request->session_id);
        
        \App\Models\Attendance::where('session_id', $session->id)
            ->update([
                'status' => 'Present',
                'marked_at' => now()->format('H:i:s')
            ]);

        $session->updateCounts();

        return response()->json([
            'success' => true,
            'message' => 'All students marked present!',
            'session_counts' => [
                'present' => $session->present_count,
                'absent' => $session->absent_count,
                'late' => $session->late_count,
                'leave' => $session->leave_count
            ]
        ]);
    }

    public function endAttendanceSession($sessionId)
    {
        $session = \App\Models\AttendanceSession::findOrFail($sessionId);
        
        $session->update([
            'end_time' => now()->format('H:i:s'),
            'status' => 'Completed'
        ]);

        $session->updateCounts();

        return response()->json([
            'success' => true,
            'message' => 'Attendance session completed!',
            'session' => $session
        ]);
    }

    public function getAttendanceSessions(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $query = \App\Models\AttendanceSession::with(['class', 'section', 'subject'])
            ->where('teacher_id', $teacher->id)
            ->orderBy('attendance_date', 'desc')
            ->orderBy('created_at', 'desc');

        if ($request->date) {
            $query->whereDate('attendance_date', $request->date);
        }

        if ($request->class_id) {
            $query->where('class_id', $request->class_id);
        }

        $sessions = $query->get();

        return response()->json([
            'success' => true,
            'data' => $sessions
        ]);
    }

    public function attendanceReport()
    {
        $teacher = auth()->guard('teacher')->user();
        
        // Get teacher's assigned classes
        $classes = \DB::table('teacher_subjects')
            ->join('classes', 'teacher_subjects.class_id', '=', 'classes.id')
            ->where('teacher_subjects.teacher_id', $teacher->id)
            ->select('classes.id', 'classes.class_name')
            ->distinct()
            ->get();
        
        return view('teacher.attendance.report', compact('teacher', 'classes'));
    }

    public function getAttendanceReportData(Request $request)
    {
        $teacher = auth()->guard('teacher')->user();
        
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        // Get all students
        $students = \App\Models\Student::where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->get();

        // Get attendance sessions in date range
        $sessions = \App\Models\AttendanceSession::where('teacher_id', $teacher->id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->whereBetween('attendance_date', [$request->start_date, $request->end_date])
            ->get();

        $report = [];
        foreach ($students as $student) {
            $attendances = \App\Models\Attendance::whereIn('session_id', $sessions->pluck('id'))
                ->where('student_id', $student->id)
                ->get();

            $totalSessions = $sessions->count();
            $presentCount = $attendances->where('status', 'Present')->count();
            $absentCount = $attendances->where('status', 'Absent')->count();
            $lateCount = $attendances->where('status', 'Late')->count();
            $leaveCount = $attendances->where('status', 'Leave')->count();
            
            $percentage = $totalSessions > 0 ? round(($presentCount / $totalSessions) * 100, 2) : 0;

            $report[] = [
                'student_id' => $student->id,
                'roll_number' => $student->roll_number,
                'name' => $student->first_name . ' ' . $student->last_name,
                'total_sessions' => $totalSessions,
                'present' => $presentCount,
                'absent' => $absentCount,
                'late' => $lateCount,
                'leave' => $leaveCount,
                'percentage' => $percentage
            ];
        }

        return response()->json([
            'success' => true,
            'data' => $report,
            'total_sessions' => $sessions->count()
        ]);
    }
}