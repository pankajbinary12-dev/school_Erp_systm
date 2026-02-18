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
        $stats = [
            'total_students' => Student::where('status', 'Active')->count(),
            'total_teachers' => Teacher::where('status', 'Active')->count(),
            'total_classes' => Classes::where('is_active', 'Active')->count(),
            'total_sections' => Section::where('is_active', 'Active')->count(),
            'total_subjects' => Subject::where('is_active', 'Active')->count(),
            'active_session' => Session::where('is_active', 'Active')->first(),
        ];

        $recent_students = Student::with(['class', 'section'])
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        $recent_teachers = Teacher::orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard-horizontal', compact('stats', 'recent_students', 'recent_teachers'));
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

        // Set status
        $validated['status'] = $request->has('status') ? 'Active' : 'Inactive';

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
    public function timetable()
    {
        return view('admin.academic.timetable');
    }

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
        return view('admin.settings.school');
    }

    public function userManagement()
    {
        return view('admin.settings.users');
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

        // Set status
        $validated['status'] = $request->has('status') ? 'Active' : 'Inactive';

        // Create admission
        $admission = \App\Models\StudentAdmission::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student admission submitted successfully!',
            'data' => $admission,
            'redirect' => route('admin.students.admissions')
        ]);
    }
}
