<?php

namespace App\Http\Controllers;

use App\Models\Exam;
use App\Models\ExamSubject;
use App\Models\StudentMark;
use App\Models\StudentResult;
use App\Models\GradeSystem;
use App\Models\Classes;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Session;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

class ExaminationController extends Controller
{
    // Dashboard
    public function dashboard()
    {
        $stats = [
            'total_exams' => Exam::count(),
            'ongoing_exams' => Exam::where('status', 'ongoing')->count(),
            'completed_exams' => Exam::where('status', 'completed')->count(),
            'published_results' => Exam::where('result_published', true)->count(),
        ];

        $recentExams = Exam::with(['class', 'session'])
            ->latest()
            ->take(10)
            ->get();

        return view('admin.examination.dashboard', compact('stats', 'recentExams'));
    }

    // Exam Management
    public function exams()
    {
        $exams = Exam::with(['class'])
            ->latest()
            ->paginate(20);
        
        $classes = Classes::orderBy('class_name')->get();
        $sessions = \App\Models\Session::all(); // Get all sessions without ordering

        return view('admin.examination.exams', compact('exams', 'classes', 'sessions'));
    }

    public function storeExam(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'exam_type' => 'required|in:unit_test,midterm,final,quarterly,half_yearly,annual',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'passing_percentage' => 'required|numeric|min:0|max:100',
        ]);

        $exam = Exam::create([
            'name' => $request->name,
            'exam_code' => Exam::generateExamCode(),
            'class_id' => $request->class_id,
            'session_id' => $request->session_id,
            'exam_type' => $request->exam_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'description' => $request->description,
            'passing_percentage' => $request->passing_percentage,
            'status' => 'scheduled',
        ]);

        return redirect()->back()->with('success', 'Exam created successfully');
    }

    public function updateExam(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'exam_type' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'passing_percentage' => 'required|numeric|min:0|max:100',
            'status' => 'required|in:scheduled,ongoing,completed,cancelled',
        ]);

        $exam = Exam::findOrFail($id);
        $exam->update($request->all());

        return redirect()->back()->with('success', 'Exam updated successfully');
    }

    // Exam Subjects
    public function examSubjects($examId)
    {
        $exam = Exam::with(['class', 'examSubjects.subject'])->findOrFail($examId);
        $subjects = Subject::orderBy('subject_name')->get();

        return view('admin.examination.exam-subjects', compact('exam', 'subjects'));
    }

    public function getExamSubjectsList($examId)
    {
        $exam = Exam::findOrFail($examId);
        $subjects = ExamSubject::with('subject')
            ->where('exam_id', $examId)
            ->get();

        return response()->json([
            'success' => true,
            'subjects' => $subjects
        ]);
    }

    public function storeExamSubject(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'subject_id' => 'required|exists:subjects,id',
            'max_marks' => 'required|numeric|min:0',
            'practical_marks' => 'nullable|numeric|min:0',
            'passing_marks' => 'required|numeric|min:0',
        ]);

        ExamSubject::create($request->all());

        // Update exam total marks
        $exam = Exam::find($request->exam_id);
        $exam->calculateTotalMarks();

        return redirect()->back()->with('success', 'Subject added to exam successfully');
    }

    public function deleteExamSubject($id)
    {
        $examSubject = ExamSubject::findOrFail($id);
        $examId = $examSubject->exam_id;
        $examSubject->delete();

        // Update exam total marks
        $exam = Exam::find($examId);
        $exam->calculateTotalMarks();

        return redirect()->back()->with('success', 'Subject removed from exam');
    }

    // Marks Entry
    public function marksEntry()
    {
        $exams = Exam::with('class')->active()->get();
        return view('admin.examination.marks-entry', compact('exams'));
    }

    public function getStudentsForMarks(Request $request)
    {
        $exam = Exam::with(['class', 'examSubjects.subject'])->findOrFail($request->exam_id);
        $examSubject = ExamSubject::with('subject')->findOrFail($request->exam_subject_id);
        
        $students = Student::where('class_id', $exam->class_id)
            ->where('status', 'Active')
            ->orderBy('roll_no')
            ->get();

        // Get existing marks
        $existingMarks = StudentMark::where('exam_id', $exam->id)
            ->where('exam_schedule_id', $examSubject->id) // Using exam_schedule_id
            ->get()
            ->keyBy('student_id');

        return response()->json([
            'success' => true,
            'exam' => $exam,
            'examSubject' => $examSubject,
            'students' => $students,
            'existingMarks' => $existingMarks
        ]);
    }

    public function saveMarks(Request $request)
    {
        $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'exam_subject_id' => 'required|exists:exam_schedules,id',
            'marks' => 'required|array',
        ]);

        DB::beginTransaction();
        try {
            foreach ($request->marks as $studentId => $markData) {
                $mark = StudentMark::updateOrCreate(
                    [
                        'exam_id' => $request->exam_id,
                        'exam_schedule_id' => $request->exam_subject_id, // Using exam_schedule_id
                        'student_id' => $studentId,
                    ],
                    [
                        'theory_marks' => $markData['theory'] ?? null,
                        'practical_marks' => $markData['practical'] ?? null,
                        'status' => $markData['status'] ?? 'present',
                        'remarks' => $markData['remarks'] ?? null,
                        'entered_by' => auth()->guard('admin')->id(),
                        'entered_at' => now(),
                    ]
                );

                $mark->calculateTotal();
            }

            DB::commit();
            return response()->json([
                'success' => true,
                'message' => 'Marks saved successfully'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Error saving marks: ' . $e->getMessage()
            ], 500);
        }
    }

    // Results
    public function results()
    {
        $exams = Exam::with('class')->completed()->get();
        return view('admin.examination.results', compact('exams'));
    }

    public function generateResults(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);
        
        DB::beginTransaction();
        try {
            $students = Student::where('class_id', $exam->class_id)
                ->where('status', 'Active')
                ->get();

            foreach ($students as $student) {
                $result = StudentResult::updateOrCreate(
                    [
                        'exam_id' => $exam->id,
                        'student_id' => $student->id,
                    ]
                );

                $result->calculateResult();
            }

            // Calculate ranks
            $results = StudentResult::where('exam_id', $exam->id)->get();
            foreach ($results as $result) {
                $result->calculateRank();
            }

            DB::commit();
            return redirect()->back()->with('success', 'Results generated successfully');

        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Error generating results: ' . $e->getMessage());
        }
    }

    public function publishResults(Request $request)
    {
        $exam = Exam::findOrFail($request->exam_id);
        $exam->result_published = true;
        $exam->result_date = now();
        $exam->save();

        // Mark all results as published
        StudentResult::where('exam_id', $exam->id)->update(['is_published' => true]);

        return redirect()->back()->with('success', 'Results published successfully');
    }

    public function viewResults($examId)
    {
        $exam = Exam::with('class')->findOrFail($examId);
        $results = StudentResult::with('student')
            ->where('exam_id', $examId)
            ->orderBy('rank')
            ->paginate(50);

        return view('admin.examination.view-results', compact('exam', 'results'));
    }

    // Report Card
    public function reportCard($examId, $studentId)
    {
        $exam = Exam::with(['class', 'examSubjects.subject'])->findOrFail($examId);
        $student = Student::with('class')->findOrFail($studentId);
        $result = StudentResult::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $marks = StudentMark::with('examSubject.subject')
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        return view('admin.examination.report-card', compact('exam', 'student', 'result', 'marks'));
    }

    public function downloadReportCard($examId, $studentId)
    {
        $exam = Exam::with(['class', 'examSubjects.subject'])->findOrFail($examId);
        $student = Student::with('class')->findOrFail($studentId);
        $result = StudentResult::where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->firstOrFail();

        $marks = StudentMark::with('examSubject.subject')
            ->where('exam_id', $examId)
            ->where('student_id', $studentId)
            ->get();

        $pdf = PDF::loadView('admin.examination.report-card-pdf', compact('exam', 'student', 'result', 'marks'))
            ->setPaper('a4', 'portrait');

        $filename = 'report_card_' . $student->admission_no . '_' . $exam->exam_code . '.pdf';
        return $pdf->download($filename);
    }

    // Grade System
    public function gradeSystem()
    {
        $grades = GradeSystem::orderBy('min_percentage', 'desc')->get();
        return view('admin.examination.grade-system', compact('grades'));
    }

    public function storeGrade(Request $request)
    {
        $request->validate([
            'grade' => 'required|string|max:10',
            'min_percentage' => 'required|numeric|min:0|max:100',
            'max_percentage' => 'required|numeric|min:0|max:100',
            'grade_point' => 'nullable|numeric|min:0|max:10',
        ]);

        GradeSystem::create($request->all());

        return redirect()->back()->with('success', 'Grade added successfully');
    }
}
