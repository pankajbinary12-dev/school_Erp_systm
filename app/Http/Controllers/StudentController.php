<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Session;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentController extends Controller
{       
    public function dashboard()
    {
        $student = auth()->guard('student')->user();
        return view('student.dashboard', compact('student'));
    }  

    public function index()
    {
        return view('admin.students.index');
    }

    public function getData(Request $request)
    {
        $query = Student::with(['class', 'section', 'session']);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('admission_no', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('roll_no', 'like', "%{$search}%");
            });
        }

        if ($request->has('class_id') && $request->class_id != '') {
            $query->where('class_id', $request->class_id);
        }

        if ($request->has('section_id') && $request->section_id != '') {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->latest()->get();

        return response()->json([
            'success' => true,
            'data' => $students
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'admission_no' => 'required|unique:students',
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'father_name' => 'required|max:100',
            'mother_name' => 'required|max:100',
            'guardian_phone' => 'required|max:20',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'session_id' => 'required|exists:sessions,id',
            'username' => 'required|unique:students|max:50',
            'password' => 'required|min:6',
            'admission_date' => 'required|date',
            'photo' => 'nullable|image|max:2048'
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['status'] = 'Active';

        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }
         
        $student = Student::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student admitted successfully!',
            'data' => $student->load(['class', 'section', 'session'])
        ]);
    }

    public function show($id)
    {
        $student = Student::with(['class', 'section', 'session'])->findOrFail($id);
        
        return response()->json([
            'success' => true,
            'data' => $student
        ]);
    }

    public function update(Request $request, $id)
    {
        $student = Student::findOrFail($id);

        $validated = $request->validate([
            'admission_no' => 'required|unique:students,admission_no,' . $id,
            'first_name' => 'required|max:100',
            'last_name' => 'required|max:100',
            'date_of_birth' => 'required|date',
            'gender' => 'required|in:Male,Female,Other',
            'father_name' => 'required|max:100',
            'mother_name' => 'nullable|max:100',
            'guardian_phone' => 'required|max:20',
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'required|exists:sections,id',
            'session_id' => 'required|exists:sessions,id',
            'username' => 'required|unique:students,username,' . $id . '|max:50',
            'photo' => 'nullable|image|max:2048',
            'admission_date' => 'required|date',
            'email' => 'nullable|email',
            'address' => 'nullable|string',
            'blood_group' => 'nullable|string',
            'status' => 'nullable|in:Active,Inactive'
        ]);

        // Handle password update
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        }

        // Handle photo removal
        if ($request->input('remove_photo') == '1' && $student->photo) {
            Storage::disk('public')->delete($student->photo);
            $validated['photo'] = null;
        }

        // Handle new photo upload
        if ($request->hasFile('photo')) {
            // Delete old photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Student updated successfully!',
            'data' => $student->load(['class', 'section', 'session'])
        ]);
    }

    public function destroy($id)
    {
        $student = Student::findOrFail($id);
        
        if ($student->photo) {
            Storage::disk('public')->delete($student->photo);
        }
        
        $student->delete();

        return response()->json([
            'success' => true,
            'message' => 'Student deleted successfully!'
        ]);
    }

    public function promote(Request $request)
    {
        $validated = $request->validate([
            'student_ids' => 'required|array',
            'student_ids.*' => 'exists:students,id',
            'new_class_id' => 'required|exists:classes,id',
            'new_section_id' => 'required|exists:sections,id',
            'new_session_id' => 'required|exists:sessions,id'
        ]);

        Student::whereIn('id', $validated['student_ids'])->update([
            'class_id' => $validated['new_class_id'],
            'section_id' => $validated['new_section_id'],
            'session_id' => $validated['new_session_id']
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Students promoted successfully!'
        ]);
    }

    // Student Portal Methods
    
    public function profile()
    {
        $student = auth()->guard('student')->user();
        return view('student.profile', compact('student'));
    }

    public function updateProfile(Request $request)
    {
        $student = auth()->guard('student')->user();

        $validated = $request->validate([
            'email' => 'nullable|email|max:100',
            'guardian_phone' => 'required|max:20',
            'address' => 'nullable|string|max:500',
            'blood_group' => 'nullable|string|max:10',
            'photo' => 'nullable|image|max:2048'
        ]);

        // Remove photo from validated data if not uploaded
        if (!$request->hasFile('photo')) {
            unset($validated['photo']);
        } else {
            // Handle photo upload
            // Delete old photo if exists
            if ($student->photo) {
                Storage::disk('public')->delete($student->photo);
            }
            $validated['photo'] = $request->file('photo')->store('students', 'public');
        }

        $student->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Profile updated successfully!'
        ]);
    }

    public function changePassword(Request $request)
    {
        $student = auth()->guard('student')->user();

        $validated = $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed'
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $student->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Current password is incorrect!'
            ], 422);
        }

        // Update password
        $student->update([
            'password' => Hash::make($validated['new_password'])
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully!'
        ]);
    }

    public function updateNotificationSettings(Request $request)
    {
        $student = auth()->guard('student')->user();

        $validated = $request->validate([
            'email_notifications' => 'boolean',
            'sms_notifications' => 'boolean',
            'assignment_alerts' => 'boolean',
            'exam_alerts' => 'boolean',
            'attendance_alerts' => 'boolean'
        ]);

        // Store in JSON column or separate table
        $student->update([
            'notification_settings' => json_encode($validated)
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Notification settings updated successfully!'
        ]);
    }

    public function subjects()
    {
        $student = auth()->guard('student')->user();
        
        $subjects = \DB::table('class_subjects')
            ->join('subjects', 'class_subjects.subject_id', '=', 'subjects.id')
            ->where('class_subjects.class_id', $student->class_id)
            ->select('subjects.*')
            ->get();
        
        return view('student.subjects', compact('student', 'subjects'));
    }

    public function attendance()
    {
        $student = auth()->guard('student')->user();
        
        // Get attendance records from attendances table (new system)
        $attendanceRecords = \DB::table('attendances')
            ->join('attendance_sessions', 'attendances.session_id', '=', 'attendance_sessions.id')
            ->where('attendances.student_id', $student->id)
            ->select(
                'attendance_sessions.attendance_date',
                'attendances.status',
                'attendances.remarks'
            )
            ->orderBy('attendance_sessions.attendance_date', 'desc')
            ->limit(30)
            ->get();
        
        // Calculate stats
        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('status', 'Present')->count();
        $percentage = $totalDays > 0 ? round(($presentDays / $totalDays) * 100, 1) : 0;
        
        return view('student.attendance', compact('student', 'attendanceRecords', 'totalDays', 'presentDays', 'percentage'));
    }

    public function assignments()
    {
        $student = auth()->guard('student')->user();
        
        // Get assignments for student's class and section
        $assignments = \DB::table('assignments')
            ->join('subjects', 'assignments.subject_id', '=', 'subjects.id')
            ->where('assignments.class_id', $student->class_id)
            ->where('assignments.section_id', $student->section_id)
            ->leftJoin('assignment_submissions', function($join) use ($student) {
                $join->on('assignment_submissions.assignment_id', '=', 'assignments.id')
                     ->where('assignment_submissions.student_id', '=', $student->id);
            })
            ->select(
                'assignments.*',
                'subjects.subject_name',
                'assignment_submissions.status as submission_status',
                'assignment_submissions.submitted_at',
                'assignment_submissions.marks_obtained'
            )
            ->orderBy('assignments.due_date', 'desc')
            ->get();
        
        return view('student.assignments', compact('student', 'assignments'));
    }

    public function results()
    {
        $student = auth()->guard('student')->user();
        
        // Check if exam_marks table exists and has data
        try {
            $results = \DB::table('exam_marks')
                ->join('subjects', 'exam_marks.subject_id', '=', 'subjects.id')
                ->where('exam_marks.student_id', $student->id)
                ->select(
                    'exam_marks.exam_id',
                    'subjects.subject_name',
                    'exam_marks.marks_obtained',
                    'exam_marks.total_marks',
                    'exam_marks.grade'
                )
                ->orderBy('exam_marks.id', 'desc')
                ->get();
        } catch (\Exception $e) {
            $results = collect(); // Empty collection if table doesn't exist
        }
        
        return view('student.results', compact('student', 'results'));
    }
}
