<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\StudentFee;
use App\Models\FeePayment;
use App\Models\StudentResult;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\StudentNotification;
use App\Models\Timetable;
use App\Models\Subject;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class StudentDashboardController extends Controller
{
    // Student Login Page
    public function loginForm()
    {
        return view('student.login');
    }

    // Student Login Process
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required',
            'password' => 'required'
        ]);

        $student = Student::where('username', $request->username)
            ->where('status', 'Active')
            ->first();

        if ($student && Hash::check($request->password, $student->password)) {
            session(['student_id' => $student->id]);
            session(['student_name' => $student->first_name . ' ' . $student->last_name]);
            
            return redirect()->route('student.dashboard');
        }

        return back()->withErrors(['error' => 'Invalid credentials or account inactive']);
    }

    // Student Logout
    public function logout()
    {
        session()->forget(['student_id', 'student_name']);
        return redirect()->route('student.login');
    }

    // Dashboard
    public function dashboard()
    {
        $studentId = session('student_id');
        $student = Student::with(['class', 'section', 'session'])->findOrFail($studentId);

        // Attendance Stats
        $today = today();
        $currentMonth = now()->month;
        $currentYear = now()->year;

        $monthlyAttendance = StudentAttendance::where('student_id', $studentId)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear)
            ->get();

        $totalAttendance = StudentAttendance::where('student_id', $studentId)->get();

        $monthlyPresent = $monthlyAttendance->where('status', 'Present')->count();
        $monthlyTotal = $monthlyAttendance->count();
        $monthlyPercentage = $monthlyTotal > 0 ? round(($monthlyPresent / $monthlyTotal) * 100, 2) : 0;

        $overallPresent = $totalAttendance->where('status', 'Present')->count();
        $overallTotal = $totalAttendance->count();
        $overallPercentage = $overallTotal > 0 ? round(($overallPresent / $overallTotal) * 100, 2) : 0;

        // Today's Attendance
        $todayAttendance = StudentAttendance::where('student_id', $studentId)
            ->whereDate('attendance_date', $today)
            ->first();

        // Fee Stats
        $feeStats = StudentFee::where('student_id', $studentId)->first();
        $totalFee = $feeStats->total_amount ?? 0;
        $paidFee = FeePayment::where('student_id', $studentId)->sum('amount');
        $dueFee = $totalFee - $paidFee;

        // Latest Result
        $latestResult = StudentResult::where('student_id', $studentId)
            ->with('exam')
            ->latest()
            ->first();

        // Pending Assignments
        $pendingAssignments = Assignment::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('due_date', '>=', now())
            ->whereDoesntHave('submissions', function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->count();

        // Unread Notifications
        $unreadNotifications = StudentNotification::where('student_id', $studentId)
            ->unread()
            ->count();

        // Recent Notifications
        $recentNotifications = StudentNotification::where('student_id', $studentId)
            ->recent()
            ->limit(5)
            ->get();

        return view('student.dashboard', compact(
            'student',
            'monthlyPercentage',
            'overallPercentage',
            'todayAttendance',
            'dueFee',
            'latestResult',
            'pendingAssignments',
            'unreadNotifications',
            'recentNotifications'
        ));
    }

    // Profile
    public function profile()
    {
        $studentId = session('student_id');
        $student = Student::with(['class', 'section', 'session'])->findOrFail($studentId);

        return view('student.profile', compact('student'));
    }

    // Update Profile
    public function updateProfile(Request $request)
    {
        $studentId = session('student_id');
        $student = Student::findOrFail($studentId);

        $validated = $request->validate([
            'email' => 'nullable|email',
            'phone' => 'nullable|string|max:20',
            'address' => 'nullable|string',
            'photo' => 'nullable|image|max:2048'
        ]);

        if ($request->hasFile('photo')) {
            // Delete old photo
            if ($student->photo && Storage::disk('public')->exists($student->photo)) {
                Storage::disk('public')->delete($student->photo);
            }
            
            $photo = $request->file('photo');
            $filename = time() . '_' . $photo->getClientOriginalName();
            $path = $photo->storeAs('students', $filename, 'public');
            $validated['photo'] = $path;
        }

        $student->update($validated);

        return back()->with('success', 'Profile updated successfully!');
    }

    // Attendance
    public function attendance()
    {
        $studentId = session('student_id');
        $student = Student::findOrFail($studentId);

        $currentMonth = now()->month;
        $currentYear = now()->year;

        $attendanceRecords = StudentAttendance::where('student_id', $studentId)
            ->whereMonth('attendance_date', $currentMonth)
            ->whereYear('attendance_date', $currentYear)
            ->orderBy('attendance_date', 'desc')
            ->get();

        $present = $attendanceRecords->where('status', 'Present')->count();
        $absent = $attendanceRecords->where('status', 'Absent')->count();
        $leave = $attendanceRecords->where('status', 'Leave')->count();
        $late = $attendanceRecords->where('status', 'Late')->count();
        $total = $attendanceRecords->count();
        $percentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;

        return view('student.attendance', compact(
            'student',
            'attendanceRecords',
            'present',
            'absent',
            'leave',
            'late',
            'total',
            'percentage'
        ));
    }

    // Subjects
    public function subjects()
    {
        $studentId = session('student_id');
        $student = Student::with(['class', 'section'])->findOrFail($studentId);

        $subjects = Subject::whereHas('classes', function($query) use ($student) {
            $query->where('classes.id', $student->class_id);
        })->with(['teachers'])->get();

        return view('student.subjects', compact('student', 'subjects'));
    }

    // Assignments
    public function assignments()
    {
        $studentId = session('student_id');
        $student = Student::findOrFail($studentId);

        $assignments = Assignment::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->with(['subject', 'teacher'])
            ->orderBy('due_date', 'desc')
            ->get();

        // Get submissions
        $submissions = AssignmentSubmission::where('student_id', $studentId)
            ->pluck('assignment_id')
            ->toArray();

        return view('student.assignments', compact('student', 'assignments', 'submissions'));
    }

    // Submit Assignment
    public function submitAssignment(Request $request, $id)
    {
        $studentId = session('student_id');
        
        $request->validate([
            'submission_text' => 'nullable|string',
            'file' => 'nullable|file|max:10240' // 10MB
        ]);

        $assignment = Assignment::findOrFail($id);

        $data = [
            'assignment_id' => $id,
            'student_id' => $studentId,
            'submission_text' => $request->submission_text,
            'submitted_at' => now(),
            'status' => 'submitted'
        ];

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $filename = time() . '_' . $file->getClientOriginalName();
            $path = $file->storeAs('assignments', $filename, 'public');
            $data['file_path'] = $path;
        }

        // Check if late
        if (now()->greaterThan($assignment->due_date)) {
            $data['status'] = 'late';
        }

        AssignmentSubmission::create($data);

        return back()->with('success', 'Assignment submitted successfully!');
    }

    // Results
    public function results()
    {
        $studentId = session('student_id');
        $student = Student::findOrFail($studentId);

        $results = StudentResult::where('student_id', $studentId)
            ->with(['exam', 'marks.subject'])
            ->orderBy('created_at', 'desc')
            ->get();

        return view('student.results', compact('student', 'results'));
    }

    // Download Report Card
    public function downloadReportCard($resultId)
    {
        $studentId = session('student_id');
        $result = StudentResult::where('id', $resultId)
            ->where('student_id', $studentId)
            ->with(['student', 'exam', 'marks.subject'])
            ->firstOrFail();

        $pdf = \PDF::loadView('student.report-card-pdf', compact('result'));
        return $pdf->download('report-card-' . $result->exam->exam_name . '.pdf');
    }

    // Fees
    public function fees()
    {
        $studentId = session('student_id');
        $student = Student::findOrFail($studentId);

        $studentFee = StudentFee::where('student_id', $studentId)
            ->with('feeStructure')
            ->first();

        $payments = FeePayment::where('student_id', $studentId)
            ->with('studentFee')
            ->orderBy('payment_date', 'desc')
            ->get();

        $totalFee = $studentFee->total_amount ?? 0;
        $paidAmount = $payments->sum('amount');
        $dueAmount = $totalFee - $paidAmount;

        return view('student.fees', compact('student', 'studentFee', 'payments', 'totalFee', 'paidAmount', 'dueAmount'));
    }

    // Download Fee Receipt
    public function downloadFeeReceipt($paymentId)
    {
        $studentId = session('student_id');
        $payment = FeePayment::where('id', $paymentId)
            ->whereHas('studentFee', function($query) use ($studentId) {
                $query->where('student_id', $studentId);
            })
            ->with(['student', 'studentFee.feeStructure'])
            ->firstOrFail();

        $pdf = \PDF::loadView('student.fee-receipt-pdf', compact('payment'));
        return $pdf->download('fee-receipt-' . $payment->receipt_number . '.pdf');
    }

    // Timetable
    public function timetable()
    {
        $studentId = session('student_id');
        $student = Student::with(['class', 'section'])->findOrFail($studentId);

        $timetable = Timetable::where('class_id', $student->class_id)
            ->where('section_id', $student->section_id)
            ->where('is_active', 'Active')
            ->with(['subject', 'teacher'])
            ->orderByRaw("FIELD(day, 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday')")
            ->orderBy('start_time')
            ->get()
            ->groupBy('day');

        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        return view('student.timetable', compact('student', 'timetable', 'days'));
    }

    // Notifications
    public function notifications()
    {
        $studentId = session('student_id');
        $student = Student::findOrFail($studentId);

        $notifications = StudentNotification::where('student_id', $studentId)
            ->recent()
            ->paginate(20);

        // Mark all as read
        StudentNotification::where('student_id', $studentId)
            ->unread()
            ->update(['is_read' => true, 'read_at' => now()]);

        return view('student.notifications', compact('student', 'notifications'));
    }
}
