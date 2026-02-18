<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Student;
use App\Models\StudentAttendance;
use App\Models\Classes;
use App\Models\Section;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    // Show student attendance page
    public function studentAttendance()
    {
        return view('admin.attendance.student');
    }

    // Load students for attendance
    public function loadStudents(Request $request)
    {
        try {
            $date = $request->input('date');
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');

            \Log::info('Loading students', ['date' => $date, 'class' => $classId, 'section' => $sectionId]);

            // Get students - try without status filter first
            $students = Student::where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->orderBy('roll_no')
                ->get();

            \Log::info('Students found: ' . $students->count());

            // Get existing attendance for this date
            $attendance = StudentAttendance::where('attendance_date', $date)
                ->where('class_id', $classId)
                ->where('section_id', $sectionId)
                ->get()
                ->keyBy('student_id');

            return response()->json([
                'success' => true,
                'students' => $students,
                'attendance' => $attendance
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Load students error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Save attendance
    public function saveAttendance(Request $request)
    {
        try {
            $date = $request->input('date');
            $classId = $request->input('class_id');
            $sectionId = $request->input('section_id');
            $attendanceData = $request->input('attendance');

            \Log::info('Saving attendance', ['count' => count($attendanceData)]);

            $saved = 0;

            foreach ($attendanceData as $record) {
                StudentAttendance::updateOrCreate(
                    [
                        'student_id' => $record['student_id'],
                        'attendance_date' => $date
                    ],
                    [
                        'class_id' => $classId,
                        'section_id' => $sectionId,
                        'status' => $record['status'],
                        'check_in_time' => $record['check_in_time'] ?? null,
                        'remarks' => $record['remarks'] ?? null,
                        'attendance_type' => 'Manual',
                        'marked_by' => auth()->guard('admin')->id()
                    ]
                );
                $saved++;
            }

            return response()->json([
                'success' => true,
                'message' => "Attendance saved for {$saved} students"
            ]);
            
        } catch (\Exception $e) {
            \Log::error('Save attendance error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    // Biometric attendance
    public function biometricScan(Request $request)
    {
        $biometricId = $request->input('biometric_id');
        $date = $request->input('date', date('Y-m-d'));

        // Find student by biometric ID (you need to add biometric_id column to students table)
        $student = Student::where('biometric_id', $biometricId)->first();

        if (!$student) {
            return response()->json([
                'success' => false,
                'message' => 'Student not found'
            ], 404);
        }

        // Mark attendance
        $attendance = StudentAttendance::updateOrCreate(
            [
                'student_id' => $student->id,
                'attendance_date' => $date
            ],
            [
                'class_id' => $student->class_id,
                'section_id' => $student->section_id,
                'status' => 'Present',
                'check_in_time' => now()->format('H:i:s'),
                'attendance_type' => 'Biometric',
                'biometric_id' => $biometricId
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Attendance marked successfully',
            'student' => $student,
            'attendance' => $attendance
        ]);
    }

    // Get attendance report
    public function getReport(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $classId = $request->input('class_id');
        $sectionId = $request->input('section_id');

        $query = StudentAttendance::with(['student', 'class', 'section'])
            ->whereBetween('attendance_date', [$startDate, $endDate]);

        if ($classId) {
            $query->where('class_id', $classId);
        }

        if ($sectionId) {
            $query->where('section_id', $sectionId);
        }

        $attendance = $query->orderBy('attendance_date', 'desc')->get();

        return response()->json([
            'success' => true,
            'data' => $attendance
        ]);
    }

    // Export attendance
    public function export(Request $request)
    {
        // Export logic here (Excel/PDF)
        return response()->json([
            'success' => true,
            'message' => 'Export functionality coming soon'
        ]);
    }
}
