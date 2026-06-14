<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Session;

class AdminController extends Controller
{
    public function dashboard()
    {
        // Basic Stats
        $stats = [
            'total_students' => Student::where('status', 'Active')->count(),
            'total_teachers' => Teacher::where('status', 'Active')->count(),
            'total_classes' => Classes::where('is_active', 'Active')->count(),
            'total_sections' => Section::where('is_active', 'Active')->count(),
            'total_subjects' => Subject::where('is_active', 'Active')->count(),
            'active_session' => Session::where('is_active', 'Active')->first(),
        ];

        // Attendance Stats (Today)
        $today = today();
        $attendanceStats = [
            'present_today' => \App\Models\StudentAttendance::whereDate('attendance_date', $today)
                ->where('status', 'Present')->count(),
            'absent_today' => \App\Models\StudentAttendance::whereDate('attendance_date', $today)
                ->where('status', 'Absent')->count(),
            'leave_today' => \App\Models\StudentAttendance::whereDate('attendance_date', $today)
                ->where('status', 'Leave')->count(),
        ];
        $attendanceStats['attendance_percentage'] = $stats['total_students'] > 0 
            ? round(($attendanceStats['present_today'] / $stats['total_students']) * 100, 2) 
            : 0;

        // Examination Stats
        $examStats = [
            'total_exams' => \App\Models\Exam::count(),
            'ongoing_exams' => \App\Models\Exam::where('status', 'ongoing')->count(),
            'completed_exams' => \App\Models\Exam::where('status', 'completed')->count(),
            'published_results' => \App\Models\Exam::where('result_published', true)->count(),
        ];

        // Fee Stats
        $feeStats = [
            'total_fee_collected' => \App\Models\FeePayment::sum('amount'),
            'pending_fees' => \App\Models\StudentFee::whereIn('status', ['pending', 'partial'])
                ->sum('due_amount'),
            'total_students_with_fees' => \App\Models\StudentFee::distinct('student_id')->count(),
            'paid_students' => \App\Models\StudentFee::where('status', 'paid')->distinct('student_id')->count(),
        ];

        // Today's Birthday - Real students whose birthday is today
        $todayBirthdays = Student::with(['class', 'section'])
            ->whereRaw('DAY(date_of_birth) = DAY(CURDATE())')
            ->whereRaw('MONTH(date_of_birth) = MONTH(CURDATE())')
            ->where('status', 'Active')
            ->orderBy('first_name')
            ->get();

        // Staff Attendance Today - Who marked attendance, when, and time
        $staffAttendanceToday = \App\Models\StaffAttendance::with(['staff'])
            ->whereDate('attendance_date', $today)
            ->orderBy('check_in', 'desc')
            ->get();

        // Student Attendance Details Today - Color-coded by status
        $studentAttendanceDetails = \App\Models\StudentAttendance::with(['student.class', 'student.section'])
            ->whereDate('attendance_date', $today)
            ->orderBy('created_at', 'desc')
            ->limit(20)
            ->get();

        // Additional Student Stats
        $studentStats = [
            'new_admissions_this_month' => Student::whereMonth('created_at', date('m'))
                ->whereYear('created_at', date('Y'))
                ->count(),
            'students_with_pending_fees' => \App\Models\StudentFee::whereIn('status', ['pending', 'partial'])
                ->distinct('student_id')
                ->count(),
            'students_below_75_attendance' => \App\Models\StudentAttendance::select('student_id')
                ->selectRaw('COUNT(*) as total_days')
                ->selectRaw('SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present_days')
                ->groupBy('student_id')
                ->havingRaw('(present_days / total_days * 100) < 75')
                ->count(),
            'total_male_students' => Student::where('status', 'Active')->where('gender', 'Male')->count(),
            'total_female_students' => Student::where('status', 'Active')->where('gender', 'Female')->count(),
        ];

        // Recent Students
        $recent_students = Student::with(['class', 'section'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Recent Teachers
        $recent_teachers = Teacher::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        // Class-wise Today's Attendance
        $classWiseAttendance = \App\Models\StudentAttendance::select('class_id', 
                \DB::raw('COUNT(*) as total'),
                \DB::raw('SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present'),
                \DB::raw('SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent'),
                \DB::raw('SUM(CASE WHEN status = "Leave" THEN 1 ELSE 0 END) as on_leave'),
                \DB::raw('SUM(CASE WHEN status = "Late" THEN 1 ELSE 0 END) as late'))
            ->with('class')
            ->whereDate('attendance_date', $today)
            ->groupBy('class_id')
            ->get();

        // Recent Exams
        $recentExams = \App\Models\Exam::with(['class'])
            ->latest()
            ->take(5)
            ->get();

        // Recent Fee Payments
        $recentPayments = \App\Models\FeePayment::with(['student', 'studentFee.feeStructure'])
            ->latest()
            ->take(5)
            ->get();

        // Leave Requests - Pending staff leave requests
        $leaveRequests = \App\Models\StaffLeave::with(['staff'])
            ->where('status', 'Pending')
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return view('admin.dashboard', compact(
            'stats', 
            'attendanceStats', 
            'examStats', 
            'feeStats',
            'studentStats',
            'todayBirthdays',
            'staffAttendanceToday',
            'studentAttendanceDetails',
            'recent_students', 
            'recent_teachers',
            'classWiseAttendance',
            'recentExams',
            'recentPayments',
            'leaveRequests'
        ));
    }

    public function students()
    {
        $students = Student::with(['class', 'section', 'session'])
            ->orderBy('admission_no')
            ->paginate(20);

        return view('admin.students.index', compact('students'));
    }

    public function allStudents()
    {
        $students = Student::with(['class', 'section'])
            ->where('status', 'Active')
            ->orderBy('admission_no')
            ->get();

        $pageTitle = 'All Students';
        return view('admin.students.all', compact('students', 'pageTitle'));
    }

    public function studentAdmission()
    {
        $pageTitle = 'Student Admission';
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sessions = Session::where('is_active', 'Active')->get();
        
        return view('admin.students.admission', compact('pageTitle', 'classes', 'sessions'));
    }

    public function allAdmissions()
    {
        $pageTitle = 'All Admissions';
        return view('admin.students.admissions', compact('pageTitle'));
    }

    public function getAdmissionsData()
    {
        try {
            $admissions = \App\Models\StudentAdmission::orderBy('created_at', 'desc')->get();
            
            // Format data for DataTables
            $formatted = $admissions->map(function($admission) {
                return [
                    'id' => $admission->id,
                    'student_name' => $admission->student_name,
                    'student_email' => $admission->student_email,
                    'class' => $admission->class ? ['class_name' => $admission->class->class_name] : null,
                    'section' => $admission->section ? ['section_name' => $admission->section->section_name] : null,
                    'father_name' => $admission->father_name,
                    'father_phone' => $admission->father_phone,
                    'admission_date' => $admission->admission_date,
                    'status' => $admission->status,
                    'created_at' => $admission->created_at,
                ];
            });

            return response()->json(['data' => $formatted]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => true,
                'message' => $e->getMessage(),
                'data' => []
            ], 500);
        }
    }

    public function editAdmission($id)
    {
        $admission = \App\Models\StudentAdmission::findOrFail($id);
        return response()->json(['success' => true, 'data' => $admission]);
    }

    public function updateAdmission(Request $request, $id)
    {
        $admission = \App\Models\StudentAdmission::findOrFail($id);

        $validated = $request->validate([
            'student_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|max:20',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'stu_address' => 'nullable|string|max:500',
            'permanent_address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:15',
            'admission_date' => 'required|date',
            'status' => 'nullable|boolean',
            'student_email' => 'required|email|max:255|unique:student_admissions,student_email,' . $id,
            'blood_group' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:50',
            'caste' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'previous_school' => 'nullable|string|max:255',
            'student_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'nullable|string|max:100',
            'father_phone' => 'required|string|max:15',
            'father_email' => 'nullable|email|max:255',
            'father_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:15',
            'mother_occupation' => 'nullable|string|max:100',
            'mother_email' => 'nullable|email|max:255',
            'mother_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:15',
            'guardian_email' => 'nullable|email|max:255',
            'emergency_contact' => 'nullable|string|max:15',
            'contact_phone' => 'nullable|string|max:15',
            'relation' => 'nullable|string|max:100',
            'previous_school_name' => 'nullable|string|max:255',
            'previous_class' => 'nullable|string|max:100',
            'tc_number' => 'nullable|string|max:100',
            'birth_certificate' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'aadhar_card_front' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'aadhar_card_back' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'medical_info' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'city_name' => 'nullable|string|max:100',
            'state_name' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
        ]);

        // Handle file uploads
        $fileFields = [
            'student_photo', 'father_photo', 'mother_photo',
            'birth_certificate', 'aadhar_card_front', 'aadhar_card_back'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                // Delete old file if exists
                if ($admission->$field && \Storage::disk('public')->exists($admission->$field)) {
                    \Storage::disk('public')->delete($admission->$field);
                }
                
                $file = $request->file($field);
                $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('admissions', $filename, 'public');
                $validated[$field] = $path;
            }
        }

        // Set status (boolean for student_admissions table)
        $validated['status'] = $request->has('status') ? 1 : 0;

        $admission->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student admission updated successfully!',
            'data' => $admission
        ]);
    }

    public function deleteAdmission($id)
    {
        $admission = \App\Models\StudentAdmission::findOrFail($id);
        $admission->delete(); // Hard delete

        return response()->json([
            'success' => true,
            'message' => 'Student admission deleted successfully!'
        ]);
    }

    public function restoreAdmission($id)
    {
        return response()->json([
            'success' => false,
            'message' => 'Restore functionality not available'
        ]);
    }

    public function trashedAdmissions()
    {
        $pageTitle = 'Trashed Admissions';
        // Redirect back to main list since we don't have soft delete
        return redirect()->route('admin.students.admissions');
    }

    public function getTrashedAdmissionsData()
    {
        return response()->json(['data' => []]);
    }

    public function addStudent()
    {
        $pageTitle = 'Add Student';
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sessions = Session::where('is_active', 'Active')->get();
        
        return view('admin.students.add', compact('pageTitle', 'classes', 'sessions'));
    }

    public function editStudent($id)
    {
        $pageTitle = 'Edit Student';
        $student = Student::with(['class', 'section', 'session'])->findOrFail($id);
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sessions = Session::where('is_active', 'Active')->get();
        
        return view('admin.students.edit', compact('pageTitle', 'student', 'classes', 'sessions'));
    }

    public function viewStudent($id)
    {
        $pageTitle = 'View Student';
        $student = Student::with(['class', 'section', 'session'])->findOrFail($id);
        
        return view('admin.students.view', compact('pageTitle', 'student'));
    }

    public function studentStrength()
    {
        $pageTitle = 'Student Strength';
        $classes = Classes::with(['sections.students' => function($query) {
            $query->where('status', 'Active');
        }])->where('is_active', 'Active')->orderBy('class_numeric')->get();
        
        return view('admin.students.strength', compact('pageTitle', 'classes'));
    }

    public function studentPromotion()
    {
        $pageTitle = 'Student Promotion';
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $sessions = Session::orderBy('start_date', 'desc')->get();
        
        return view('admin.students.promotion', compact('pageTitle', 'classes', 'sessions'));
    }

    public function searchStudentsForPromotion(Request $request)
    {
        $students = Student::where('session_id', $request->session_id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('status', 'Active')
            ->orderBy('roll_no')
            ->get();

        $fromClass = Classes::find($request->class_id);
        $fromSection = Section::find($request->section_id);

        return response()->json([
            'students' => $students,
            'fromClass' => $fromClass->class_name,
            'fromSection' => $fromSection->section_name
        ]);
    }

    public function promoteStudents(Request $request)
    {
        $request->validate([
            'student_ids' => 'required|array',
            'to_session_id' => 'required|exists:sessions,id',
            'to_class_id' => 'required|exists:classes,id',
            'to_section_id' => 'required|exists:sections,id'
        ]);

        $promotedCount = 0;

        foreach ($request->student_ids as $studentId) {
            $student = Student::find($studentId);
            if ($student) {
                $student->update([
                    'session_id' => $request->to_session_id,
                    'class_id' => $request->to_class_id,
                    'section_id' => $request->to_section_id
                ]);
                $promotedCount++;
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Students promoted successfully',
            'promoted_count' => $promotedCount
        ]);
    }

    public function studentDetails()
    {
        $pageTitle = 'Student Details';
        $classes = Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        
        return view('admin.students.details', compact('pageTitle', 'classes'));
    }

    // Staff Methods
    public function allStaff()
    {
        $teachers = Teacher::where('status', 'Active')->orderBy('employee_id')->get();
        return view('admin.staff.all', compact('teachers'));
    }

    public function addStaff()
    {
        return view('admin.staff.add');
    }

    public function staffAttendance()
    {
        return view('admin.staff.attendance');
    }

    public function staffLeave()
    {
        return view('admin.staff.leave');
    }

    // Attendance Methods
    public function studentAttendance()
    {
        return view('admin.attendance.student');
    }

    public function staffAttendanceView()
    {
        return view('admin.attendance.staff');
    }

    public function attendanceReport()
    {
        return view('admin.attendance.report');
    }

    // Academic Methods
    // public function timetable()
    // {
    //     return view('admin.academic.timetable');
    // }

    // Examination Methods
    public function exams()
    {
        return view('admin.exams.index');
    }

    public function examSchedule()
    {
        return view('admin.exams.schedule');
    }

    public function examMarks()
    {
        return view('admin.exams.marks');
    }

    public function examResults()
    {
        return view('admin.exams.results');
    }

    // Library Method
    public function library()
    {
        return view('admin.library.index');
    }

    // Fees Methods
    public function collectFees()
    {
        return view('admin.fees.collect');
    }

    public function feeStructure()
    {
        return view('admin.fees.structure');
    }

    public function feeReport()
    {
        return view('admin.fees.report');
    }

    // Settings Methods
    public function generalSettings()
    {
        return view('admin.settings.general');
    }

    public function schoolInfo()
    {
        $school = \App\Models\SchoolSetting::first();
        return view('admin.settings.school', compact('school'));
    }

    public function updateSchoolInfo(Request $request)
    {
        $validated = $request->validate([
            'school_name' => 'required|max:200',
            'school_code' => 'nullable|max:50',
            'email' => 'nullable|email|max:100',
            'phone' => 'nullable|max:20',
            'mobile' => 'nullable|max:20',
            'address' => 'nullable',
            'city' => 'nullable|max:100',
            'state' => 'nullable|max:100',
            'pincode' => 'nullable|max:10',
            'website' => 'nullable|max:200',
            'logo' => 'nullable|image|max:2048',
            'favicon' => 'nullable|image|max:1024',
            'header_image' => 'nullable|image|max:3072',
            'principal_name' => 'nullable|max:100',
            'principal_signature' => 'nullable|image|max:1024',
            'affiliation_no' => 'nullable|max:100',
            'board' => 'nullable|max:100',
            'about' => 'nullable'
        ]);

        $school = \App\Models\SchoolSetting::first();
        
        if (!$school) {
            $school = new \App\Models\SchoolSetting();
        }

        // Handle file uploads
        if ($request->hasFile('logo')) {
            if ($school->logo) {
                \Storage::disk('public')->delete($school->logo);
            }
            $validated['logo'] = $request->file('logo')->store('school', 'public');
        }

        if ($request->hasFile('favicon')) {
            if ($school->favicon) {
                \Storage::disk('public')->delete($school->favicon);
            }
            $validated['favicon'] = $request->file('favicon')->store('school', 'public');
        }

        if ($request->hasFile('header_image')) {
            if ($school->header_image) {
                \Storage::disk('public')->delete($school->header_image);
            }
            $validated['header_image'] = $request->file('header_image')->store('school', 'public');
        }

        if ($request->hasFile('principal_signature')) {
            if ($school->principal_signature) {
                \Storage::disk('public')->delete($school->principal_signature);
            }
            $validated['principal_signature'] = $request->file('principal_signature')->store('school', 'public');
        }

        $school->fill($validated);
        $school->save();

        return response()->json([
            'success' => true,
            'message' => 'School information updated successfully!'
        ]);
    }

    public function userManagement()
    {
        return view('admin.settings.user-management');
    }

    public function getUsersData(Request $request)
    {
        $type = $request->get('type');
        $users = [];

        if (!$type || $type === 'admin') {
            $admins = \App\Models\Admin::all()->map(function($admin) {
                return [
                    'id' => $admin->id,
                    'type' => 'admin',
                    'name' => $admin->username,
                    'email' => $admin->email ?? 'N/A',
                    'password' => $admin->password,
                    'status' => 'Active',
                    'last_login' => $this->getLastLogin('admin', $admin->id)
                ];
            });
            $users = array_merge($users, $admins->toArray());
        }

        if (!$type || $type === 'teacher') {
            $teachers = \App\Models\Teacher::all()->map(function($teacher) {
                return [
                    'id' => $teacher->id,
                    'type' => 'teacher',
                    'name' => $teacher->first_name . ' ' . $teacher->last_name,
                    'email' => $teacher->email ?? 'N/A',
                    'password' => $teacher->password,
                    'status' => $teacher->status,
                    'last_login' => $this->getLastLogin('teacher', $teacher->id)
                ];
            });
            $users = array_merge($users, $teachers->toArray());
        }

        if (!$type || $type === 'student') {
            $students = \App\Models\Student::all()->map(function($student) {
                return [
                    'id' => $student->id,
                    'type' => 'student',
                    'name' => $student->full_name,
                    'email' => $student->email ?? 'N/A',
                    'password' => $student->password,
                    'status' => $student->status,
                    'last_login' => $this->getLastLogin('student', $student->id)
                ];
            });
            $users = array_merge($users, $students->toArray());
        }

        if (!$type || $type === 'staff') {
            $staff = \App\Models\StaffMember::all()->map(function($member) {
                return [
                    'id' => $member->id,
                    'type' => 'staff',
                    'name' => $member->first_name . ' ' . $member->last_name,
                    'email' => $member->email ?? 'N/A',
                    'password' => $member->password ?? 'N/A',
                    'status' => $member->status ?? 'Active',
                    'last_login' => $this->getLastLogin('staff', $member->id)
                ];
            });
            $users = array_merge($users, $staff->toArray());
        }

        return response()->json(['users' => $users]);
    }

    private function getLastLogin($userType, $userId)
    {
        $log = \DB::table('login_logs')
            ->where('user_type', $userType)
            ->where('user_id', $userId)
            ->where('status', 'success')
            ->orderBy('login_at', 'desc')
            ->first();

        return $log ? \Carbon\Carbon::parse($log->login_at)->format('d/m/Y H:i') : 'Never';
    }

    public function getLoginHistory(Request $request)
    {
        $query = \DB::table('login_logs')->orderBy('login_at', 'desc');

        if ($request->get('type')) {
            $query->where('user_type', $request->get('type'));
        }

        if ($request->get('date')) {
            $query->whereDate('login_at', $request->get('date'));
        }

        $logs = $query->limit(100)->get();

        return response()->json(['logs' => $logs]);
    }

    public function resetUserPassword(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required',
            'user_type' => 'required|in:admin,teacher,student,staff',
            'new_password' => 'required|min:6'
        ]);

        $hashedPassword = \Hash::make($validated['new_password']);

        switch ($validated['user_type']) {
            case 'admin':
                \App\Models\Admin::find($validated['user_id'])->update(['password' => $hashedPassword]);
                break;
            case 'teacher':
                \App\Models\Teacher::find($validated['user_id'])->update(['password' => $hashedPassword]);
                break;
            case 'student':
                \App\Models\Student::find($validated['user_id'])->update(['password' => $hashedPassword]);
                break;
            case 'staff':
                \App\Models\StaffMember::find($validated['user_id'])->update(['password' => $hashedPassword]);
                break;
        }

        return response()->json([
            'success' => true,
            'message' => 'Password reset successfully!'
        ]);
    }

    public function exportLoginHistory(Request $request)
    {
        $query = \DB::table('login_logs')->orderBy('login_at', 'desc');

        if ($request->get('type')) {
            $query->where('user_type', $request->get('type'));
        }

        if ($request->get('date')) {
            $query->whereDate('login_at', $request->get('date'));
        }

        $logs = $query->get();

        $filename = 'login_history_' . date('Y-m-d_His') . '.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ];

        $callback = function() use ($logs) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Date/Time', 'User Type', 'Username', 'IP Address', 'Device', 'Browser', 'OS', 'Status']);

            foreach ($logs as $log) {
                fputcsv($file, [
                    $log->login_at,
                    $log->user_type,
                    $log->username,
                    $log->ip_address,
                    $log->device_type,
                    $log->browser,
                    $log->os,
                    $log->status
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function classes()
    {
        $classes = Classes::with('sections')
            ->orderBy('class_numeric')
            ->get();

        return view('admin.classes.index', compact('classes'));
    }

    public function subjects()
    {
        $subjects = Subject::orderBy('subject_name')
            ->get();

        return view('admin.subjects.index', compact('subjects'));
    }

    public function sessions()
    {
        $sessions = Session::orderBy('start_date', 'desc')
            ->get();

        return view('admin.sessions.index', compact('sessions'));
    }

    // API endpoints for AJAX
    public function getStats()
    {
        return response()->json([
            'students' => Student::where('status', 'Active')->count(),
            'teachers' => Teacher::where('status', 'Active')->count(),
            'classes' => Classes::where('is_active', 'Active')->count(),
            'sections' => Section::where('is_active', 'Active')->count(),
            'subjects' => Subject::where('is_active', 'Active')->count(),
            'session' => Session::where('is_active', 'Active')->first()->session_name ?? 'N/A',
        ]);
    }

    public function getRecentStudents()
    {
        $students = Student::with(['class', 'section'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        return response()->json($students);
    }

    // Generate Admission Number
    public function generateAdmissionNumber(Request $request)
    {
        $year = $request->input('year', date('Y'));
        
        // Get the last admission number for this year
        $lastAdmission = \App\Models\StudentAdmission::where('admission_no', 'LIKE', $year . '%')
            ->orderBy('admission_no', 'desc')
            ->first();
        
        if ($lastAdmission) {
            // Extract the sequence number from last admission
            $lastNumber = intval(substr($lastAdmission->admission_no, 4)); // Get last 4 digits
            $nextNumber = $lastNumber + 1;
        } else {
            // First admission of the year
            $nextNumber = 1;
        }
        
        // Format: YYYY0001, YYYY0002, etc.
        $admissionNo = $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        
        return response()->json([
            'success' => true,
            'admission_no' => $admissionNo,
            'year' => $year,
            'sequence' => $nextNumber
        ]);
    }

    // Student Admission Store
    public function storeAdmission(Request $request)
    {
        $validated = $request->validate([
            'admission_no' => 'required|string|unique:student_admissions,admission_no',
            'student_name' => 'required|string|max:255',
            'dob' => 'required|date',
            'gender' => 'required|string|max:20',
            'class_id' => 'nullable|integer',
            'section_id' => 'nullable|integer',
            'stu_address' => 'nullable|string|max:500',
            'permanent_address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:15',
            'admission_date' => 'required|date',
            'status' => 'nullable|string|max:50',
            'student_email' => 'required|email|max:255|unique:student_admissions,student_email',
            'blood_group' => 'nullable|string|max:10',
            'religion' => 'nullable|string|max:50',
            'caste' => 'nullable|string|max:50',
            'nationality' => 'nullable|string|max:50',
            'previous_school' => 'nullable|string|max:255',
            'student_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'father_name' => 'required|string|max:255',
            'father_occupation' => 'nullable|string|max:100',
            'father_phone' => 'required|string|max:15',
            'father_email' => 'nullable|email|max:255',
            'father_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'mother_name' => 'required|string|max:255',
            'mother_phone' => 'required|string|max:15',
            'mother_occupation' => 'nullable|string|max:100',
            'mother_email' => 'nullable|email|max:255',
            'mother_photo' => 'nullable|image|mimes:jpeg,jpg,png,gif,webp|max:5120',
            'guardian_name' => 'nullable|string|max:255',
            'guardian_phone' => 'nullable|string|max:15',
            'guardian_email' => 'nullable|email|max:255',
            'emergency_contact' => 'nullable|string|max:15',
            'contact_phone' => 'nullable|string|max:15',
            'relation' => 'nullable|string|max:100',
            'previous_school_name' => 'nullable|string|max:255',
            'previous_class' => 'nullable|string|max:100',
            'tc_number' => 'nullable|string|max:100',
            'birth_certificate' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'aadhar_card_front' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'aadhar_card_back' => 'nullable|file|mimes:jpeg,jpg,png,pdf|max:5120',
            'medical_info' => 'nullable|string|max:1000',
            'allergies' => 'nullable|string|max:1000',
            'city_name' => 'nullable|string|max:100',
            'state_name' => 'nullable|string|max:100',
            'pin_code' => 'nullable|string|max:20',
        ]);

        // Handle file uploads
        $fileFields = [
            'student_photo', 'father_photo', 'mother_photo',
            'birth_certificate', 'aadhar_card_front', 'aadhar_card_back'
        ];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $file = $request->file($field);
                $filename = time() . '_' . $field . '_' . $file->getClientOriginalName();
                $path = $file->storeAs('admissions', $filename, 'public');
                $validated[$field] = $path;
            }
        }

        // Set status (boolean for student_admissions table)
        $validated['status'] = $request->has('status') ? 1 : 0;

        // Create admission
        $admission = \App\Models\StudentAdmission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student admission submitted successfully!',
            'data' => $admission,
            'redirect' => route('admin.students.admissions')
        ]);
    }

    // Teacher Management Methods
    public function teachers()
    {
        return view('admin.teachers.index');
    }

    public function getTeachersData()
    {
        $teachers = \App\Models\Teacher::orderBy('first_name')->get();
        
        // Add assigned classes count
        $teachers->each(function($teacher) {
            $teacher->assigned_count = \DB::table('teacher_subjects')
                ->where('teacher_id', $teacher->id)
                ->count();
        });

        return response()->json([
            'success' => true,
            'data' => $teachers
        ]);
    }

    public function assignClasses($id)
    {
        $teacher = \App\Models\Teacher::findOrFail($id);
        $classes = \App\Models\Classes::where('is_active', 'Active')->orderBy('class_numeric')->get();
        $subjects = \App\Models\Subject::where('is_active', 'Active')->orderBy('subject_name')->get();
        
        return view('admin.teachers.assign-classes', compact('teacher', 'classes', 'subjects'));
    }

    public function getAssignedClasses($id)
    {
        $assignments = \DB::table('teacher_subjects')
            ->join('classes', 'teacher_subjects.class_id', '=', 'classes.id')
            ->join('sections', 'teacher_subjects.section_id', '=', 'sections.id')
            ->join('subjects', 'teacher_subjects.subject_id', '=', 'subjects.id')
            ->where('teacher_subjects.teacher_id', $id)
            ->select(
                'teacher_subjects.id',
                'classes.class_name',
                'sections.section_name',
                'subjects.subject_name'
            )
            ->get();

        return response()->json([
            'success' => true,
            'data' => $assignments
        ]);
    }

    public function storeAssignedClasses(Request $request, $id)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'subject_id' => 'required|exists:subjects,id'
        ]);

        // Check if already assigned
        $exists = \DB::table('teacher_subjects')
            ->where('teacher_id', $id)
            ->where('class_id', $request->class_id)
            ->where('section_id', $request->section_id)
            ->where('subject_id', $request->subject_id)
            ->exists();

        if ($exists) {
            return response()->json([
                'success' => false,
                'message' => 'This assignment already exists!'
            ], 400);
        }

        \DB::table('teacher_subjects')->insert([
            'teacher_id' => $id,
            'class_id' => $request->class_id,
            'section_id' => $request->section_id,
            'subject_id' => $request->subject_id,
            'created_at' => now(),
            'updated_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Class assigned successfully!'
        ]);
    }

    public function deleteAssignment($id)
    {
        \DB::table('teacher_subjects')->where('id', $id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Assignment removed successfully!'
        ]);
    }

    // ID Card Management
    public function idCards()
    {
        return view('admin.id-cards.index');
    }

    public function idCardTemplates()
    {
        $templates = \App\Models\IdCardTemplate::all();
        return view('admin.id-cards.templates', compact('templates'));
    }

    public function getTemplatesData()
    {
        $templates = \App\Models\IdCardTemplate::latest()->get();
        return response()->json([
            'success' => true,
            'data' => $templates
        ]);
    }

    public function storeTemplate(Request $request)
    {
        $validated = $request->validate([
            'template_name' => 'required|max:100',
            'border_style' => 'required|in:modern,classic,colorful,minimal',
            'border_color' => 'required|max:20',
            'background_color' => 'required|max:20',
            'text_color' => 'required|max:20',
            'header_bg_color' => 'required|max:20',
            'show_logo' => 'boolean',
            'show_qr_code' => 'boolean',
            'show_barcode' => 'boolean'
        ]);

        $template = \App\Models\IdCardTemplate::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template created successfully!',
            'data' => $template
        ]);
    }

    public function updateTemplate(Request $request, $id)
    {
        $template = \App\Models\IdCardTemplate::findOrFail($id);

        $validated = $request->validate([
            'template_name' => 'required|max:100',
            'border_style' => 'required|in:modern,classic,colorful,minimal',
            'border_color' => 'required|max:20',
            'background_color' => 'required|max:20',
            'text_color' => 'required|max:20',
            'header_bg_color' => 'required|max:20',
            'show_logo' => 'boolean',
            'show_qr_code' => 'boolean',
            'show_barcode' => 'boolean'
        ]);

        $template->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Template updated successfully!',
            'data' => $template
        ]);
    }

    public function deleteTemplate($id)
    {
        \App\Models\IdCardTemplate::findOrFail($id)->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully!'
        ]);
    }

    public function generateIdCard(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'template_id' => 'required|exists:id_card_templates,id'
        ]);

        $students = Student::whereIn('id', $validated['student_ids'])
            ->with(['class', 'section'])
            ->get();
        
        $template = \App\Models\IdCardTemplate::findOrFail($validated['template_id']);

        return view('admin.id-cards.generate-simple', compact('students', 'template'));
    }

    // Calendar
    public function calendar()
    {
        return view('admin.calendar.index');
    }

    public function getCalendarEvents()
    {
        // Get events from different sources
        $events = [];

        // Exams
        $exams = \DB::table('exams')->get();
        foreach ($exams as $exam) {
            $events[] = [
                'title' => 'Exam',
                'start' => $exam->exam_date ?? date('Y-m-d'),
                'color' => '#dc3545',
                'type' => 'exam'
            ];
        }

        // Holidays (you can create a holidays table)
        // Staff leaves
        $leaves = \DB::table('staff_leaves')
            ->where('status', 'Approved')
            ->get();
        foreach ($leaves as $leave) {
            $events[] = [
                'title' => 'Staff Leave',
                'start' => $leave->start_date,
                'end' => $leave->end_date,
                'color' => '#ffc107',
                'type' => 'leave'
            ];
        }

        return response()->json($events);
    }

    // Admin Profile
    public function profile()
    {
        $admin = auth()->guard('admin')->user();
        return view('admin.profile.index', compact('admin'));
    }

    public function updateProfile(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        $validated = $request->validate([
            'username' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:admins,email,' . $admin->id,
            'phone' => 'nullable|string|max:20',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            if ($admin->photo) {
                \Storage::disk('public')->delete($admin->photo);
            }
            $validated['photo'] = $request->file('photo')->store('admin_photos', 'public');
        }

        $admin->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    public function changePassword(Request $request)
    {
        $admin = auth()->guard('admin')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);

        if (!\Hash::check($request->current_password, $admin->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect!'
            ], 400);
        }

        $admin->update([
            'password' => \Hash::make($request->new_password)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }
}

