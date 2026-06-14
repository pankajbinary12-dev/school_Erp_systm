<?php

namespace App\Http\Controllers;

use App\Models\StudentAttendance;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StudentAttendanceController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $today = today();
        
        $stats = [
            'total_students' => Student::where('status', 'Active')->count(),
            'present_today' => StudentAttendance::today()->present()->count(),
            'absent_today' => StudentAttendance::today()->absent()->count(),
            'leave_today' => StudentAttendance::today()->where('status', 'Leave')->count(),
        ];

        $stats['attendance_percentage'] = $stats['total_students'] > 0 
            ? round(($stats['present_today'] / $stats['total_students']) * 100, 2) 
            : 0;

        // Recent attendance
        $recentAttendance = StudentAttendance::with(['student', 'class'])
            ->latest('attendance_date')
            ->take(10)
            ->get();

        // Class-wise today's attendance
        $classWiseAttendance = StudentAttendance::select('class_id', 
                DB::raw('COUNT(*) as total'),
                DB::raw('SUM(CASE WHEN status = "Present" THEN 1 ELSE 0 END) as present'),
                DB::raw('SUM(CASE WHEN status = "Absent" THEN 1 ELSE 0 END) as absent'))
            ->with('class')
            ->whereDate('attendance_date', $today)
            ->groupBy('class_id')
            ->get();

        return view('admin.attendance.student.dashboard', compact('stats', 'recentAttendance', 'classWiseAttendance'));
    }

    // Mark Attendance Page
    public function markAttendance()
    {
        $classes = Classes::orderBy('class_name')->get();
        return view('admin.attendance.student.mark', compact('classes'));
    }

    // Get Students by Class and Section
    public function getStudents(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date'
        ]);

        $query = Student::where('class_id', $request->class_id)
            ->where('status', 'Active')
            ->orderBy('roll_no');

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->get()->map(function($student) {
            return [
                'id' => $student->id,
                'roll_no' => $student->roll_no,
                'name' => $student->first_name . ' ' . $student->last_name,
                'admission_no' => $student->admission_no,
                'first_name' => $student->first_name,
                'last_name' => $student->last_name,
            ];
        });

        // Get existing attendance for the date
        $existingAttendance = StudentAttendance::where('class_id', $request->class_id)
            ->whereDate('attendance_date', $request->attendance_date);

        if ($request->section_id) {
            $existingAttendance->where('section_id', $request->section_id);
        }

        $existingAttendance = $existingAttendance->get()->keyBy('student_id');

        return response()->json([
            'success' => true,
            'students' => $students,
            'existingAttendance' => $existingAttendance
        ]);
    }

    // Save Attendance
    public function saveAttendance(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'attendance_date' => 'required|date',
            'attendance' => 'required|array'
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->attendance as $studentId => $data) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $studentId,
                        'attendance_date' => $request->attendance_date,
                    ],
                    [
                        'class_id' => $request->class_id,
                        'section_id' => $request->section_id,
                        'status' => $data['status'],
                        'remarks' => $data['remarks'] ?? null,
                        'attendance_type' => 'manual',
                        'marked_by' => auth()->guard('admin')->id(),
                    ]
                );
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Attendance saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving attendance: ' . $e->getMessage()
            ], 500);
        }
    }

    // View Attendance
    public function viewAttendance()
    {
        $classes = Classes::orderBy('class_name')->get();
        return view('admin.attendance.student.view', compact('classes'));
    }

    // Get Attendance Records
    public function getAttendanceRecords(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        $query = StudentAttendance::with(['student', 'class', 'section'])
            ->where('class_id', $request->class_id)
            ->whereBetween('attendance_date', [$request->start_date, $request->end_date]);

        if ($request->section_id) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        $records = $query->orderBy('attendance_date', 'desc')
            ->orderBy('student_id')
            ->paginate(50);

        // Add full name to each student
        $records->getCollection()->transform(function($record) {
            if ($record->student) {
                $record->student->name = $record->student->first_name . ' ' . $record->student->last_name;
            }
            return $record;
        });

        return response()->json([
            'success' => true,
            'records' => $records
        ]);
    }

    // Reports
    public function reports()
    {
        $classes = Classes::orderBy('class_name')->get();
        return view('admin.attendance.student.reports', compact('classes'));
    }

    // Generate Report
    public function generateReport(Request $request)
    {
        $request->validate([
            'report_type' => 'required|in:daily,monthly,student_wise,defaulters',
            'class_id' => 'required|exists:classes,id',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        switch ($request->report_type) {
            case 'daily':
                return $this->dailyReport($request);
            case 'monthly':
                return $this->monthlyReport($request);
            case 'student_wise':
                return $this->studentWiseReport($request);
            case 'defaulters':
                return $this->defaultersReport($request);
        }
    }

    // Daily Report
    private function dailyReport($request)
    {
        $date = $request->start_date;
        $classId = $request->class_id;

        $students = Student::where('class_id', $classId)
            ->where('status', 'Active')
            ->orderBy('roll_no')
            ->get();

        $attendance = StudentAttendance::where('class_id', $classId)
            ->whereDate('attendance_date', $date)
            ->get()
            ->keyBy('student_id');

        $class = Classes::find($classId);

        $data = [
            'date' => Carbon::parse($date)->format('d M Y'),
            'class' => $class,
            'students' => $students,
            'attendance' => $attendance,
            'stats' => [
                'total' => $students->count(),
                'present' => $attendance->where('status', 'Present')->count(),
                'absent' => $attendance->where('status', 'Absent')->count(),
                'leave' => $attendance->where('status', 'Leave')->count(),
            ]
        ];

        if ($request->format == 'pdf') {
            $pdf = PDF::loadView('admin.attendance.student.reports.daily-pdf', $data);
            return $pdf->download('daily_attendance_' . $date . '.pdf');
        }

        return view('admin.attendance.student.reports.daily', $data);
    }

    // Monthly Report
    private function monthlyReport($request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $classId = $request->class_id;

        $students = Student::where('class_id', $classId)
            ->where('status', 'Active')
            ->orderBy('roll_no')
            ->get();

        $dates = [];
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);

        while ($current <= $end) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }

        $attendance = StudentAttendance::where('class_id', $classId)
            ->whereBetween('attendance_date', [$startDate, $endDate])
            ->get()
            ->groupBy('student_id');

        $class = Classes::find($classId);

        $data = [
            'start_date' => Carbon::parse($startDate)->format('d M Y'),
            'end_date' => Carbon::parse($endDate)->format('d M Y'),
            'class' => $class,
            'students' => $students,
            'dates' => $dates,
            'attendance' => $attendance,
            'total_days' => count($dates)
        ];

        if ($request->format == 'pdf') {
            $pdf = PDF::loadView('admin.attendance.student.reports.monthly-pdf', $data)
                ->setPaper('a4', 'landscape');
            return $pdf->download('monthly_attendance_' . $startDate . '_to_' . $endDate . '.pdf');
        }

        return view('admin.attendance.student.reports.monthly', $data);
    }

    // Student-wise Report
    private function studentWiseReport($request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $classId = $request->class_id;

        $students = Student::where('class_id', $classId)
            ->where('status', 'Active')
            ->orderBy('roll_no')
            ->get();

        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        $studentStats = [];
        foreach ($students as $student) {
            $attendance = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            $present = $attendance->where('status', 'Present')->count();
            $absent = $attendance->where('status', 'Absent')->count();
            $leave = $attendance->where('status', 'Leave')->count();
            $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100, 2) : 0;

            $studentStats[] = [
                'student' => $student,
                'present' => $present,
                'absent' => $absent,
                'leave' => $leave,
                'total_days' => $totalDays,
                'percentage' => $percentage
            ];
        }

        $class = Classes::find($classId);

        $data = [
            'start_date' => Carbon::parse($startDate)->format('d M Y'),
            'end_date' => Carbon::parse($endDate)->format('d M Y'),
            'class' => $class,
            'studentStats' => $studentStats,
            'total_days' => $totalDays
        ];

        if ($request->format == 'pdf') {
            $pdf = PDF::loadView('admin.attendance.student.reports.student-wise-pdf', $data);
            return $pdf->download('student_wise_attendance.pdf');
        }

        return view('admin.attendance.student.reports.student-wise', $data);
    }

    // Defaulters Report (<75%)
    private function defaultersReport($request)
    {
        $startDate = $request->start_date;
        $endDate = $request->end_date;
        $classId = $request->class_id;
        $threshold = $request->threshold ?? 75;

        $students = Student::where('class_id', $classId)
            ->where('status', 'Active')
            ->orderBy('roll_no')
            ->get();

        $totalDays = Carbon::parse($startDate)->diffInDays(Carbon::parse($endDate)) + 1;

        $defaulters = [];
        foreach ($students as $student) {
            $attendance = StudentAttendance::where('student_id', $student->id)
                ->whereBetween('attendance_date', [$startDate, $endDate])
                ->get();

            $present = $attendance->where('status', 'Present')->count();
            $percentage = $totalDays > 0 ? round(($present / $totalDays) * 100, 2) : 0;

            if ($percentage < $threshold) {
                $defaulters[] = [
                    'student' => $student,
                    'present' => $present,
                    'absent' => $attendance->where('status', 'Absent')->count(),
                    'total_days' => $totalDays,
                    'percentage' => $percentage
                ];
            }
        }

        $class = Classes::find($classId);

        $data = [
            'start_date' => Carbon::parse($startDate)->format('d M Y'),
            'end_date' => Carbon::parse($endDate)->format('d M Y'),
            'class' => $class,
            'defaulters' => $defaulters,
            'total_days' => $totalDays,
            'threshold' => $threshold
        ];

        if ($request->format == 'pdf') {
            $pdf = PDF::loadView('admin.attendance.student.reports.defaulters-pdf', $data);
            return $pdf->download('attendance_defaulters.pdf');
        }

        return view('admin.attendance.student.reports.defaulters', $data);
    }

    // Get Sections by Class
    public function getSections($classId)
    {
        $sections = Section::where('class_id', $classId)->orderBy('section_name')->get();
        return response()->json(['success' => true, 'sections' => $sections]);
    }
}
