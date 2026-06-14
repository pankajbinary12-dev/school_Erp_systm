<?php

namespace App\Http\Controllers;

use App\Models\Certificate;
use App\Models\Student;
use App\Models\Classes;
use App\Models\Section;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateController extends Controller
{
    // List all certificates
    public function index(Request $request)
    {
        $query = Certificate::with(['student', 'issuedBy']);

        // Filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $certificates = $query->latest()->paginate(20);
        $types = Certificate::types();

        return view('admin.certificates.index', compact('certificates', 'types'));
    }

    // Show create form
    public function create()
    {
        $students = Student::where('status', 'Active')
            ->orderBy('first_name')
            ->get();
        
        $classes = Classes::orderBy('class_name')->get();
        $types = Certificate::types();

        return view('admin.certificates.create', compact('students', 'classes', 'types'));
    }

    // Store certificate
    public function store(Request $request)
    {
        $request->validate([
            'student_id' => 'required|exists:students,id',
            'type' => 'required|in:bonafide,transfer,character,fee,migration',
            'issue_date' => 'required|date',
            'remarks' => 'nullable|string|max:500'
        ]);

        $student = Student::findOrFail($request->student_id);
        
        // Generate certificate number
        $certificateNo = Certificate::generateCertificateNumber($request->type);
        
        // Generate content
        $content = $this->generateContent($student, $request->type);
        
        // Generate QR Code
        $qrCode = $this->generateQRCode($certificateNo);

        $certificate = Certificate::create([
            'student_id' => $request->student_id,
            'type' => $request->type,
            'certificate_no' => $certificateNo,
            'issue_date' => $request->issue_date,
            'content' => $content,
            'qr_code' => $qrCode,
            'issued_by' => auth()->guard('admin')->id(),
            'remarks' => $request->remarks
        ]);

        return redirect()
            ->route('admin.certificates.show', $certificate->id)
            ->with('success', 'Certificate generated successfully!');
    }

    // Show certificate
    public function show($id)
    {
        $certificate = Certificate::with(['student', 'issuedBy'])->findOrFail($id);
        return view('admin.certificates.show', compact('certificate'));
    }

    // Download PDF
    public function downloadPDF($id)
    {
        $certificate = Certificate::with(['student', 'issuedBy'])->findOrFail($id);
        
        $pdf = PDF::loadView('admin.certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'portrait');

        // Clean filename - replace / and \ with -
        $certNo = str_replace(['/', '\\'], '-', $certificate->certificate_no);
        $studentName = str_replace(['/', '\\', ' '], '_', $certificate->student->first_name);
        $filename = $certNo . '_' . $studentName . '.pdf';
        
        return $pdf->download($filename);
    }

    // Preview PDF
    public function preview($id)
    {
        $certificate = Certificate::with(['student', 'issuedBy'])->findOrFail($id);
        
        $pdf = PDF::loadView('admin.certificates.pdf', compact('certificate'))
            ->setPaper('a4', 'portrait');

        return $pdf->stream();
    }

    // Bulk generation form
    public function bulkCreate()
    {
        $classes = Classes::orderBy('class_name')->get();
        $types = Certificate::types();

        return view('admin.certificates.bulk', compact('classes', 'types'));
    }

    // Bulk generation process
    public function bulkStore(Request $request)
    {
        $request->validate([
            'class_id' => 'required|exists:classes,id',
            'section_id' => 'nullable|exists:sections,id',
            'type' => 'required|in:bonafide,transfer,character,fee,migration',
            'issue_date' => 'required|date'
        ]);

        $query = Student::where('class_id', $request->class_id)
            ->where('status', 'Active');

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        $students = $query->get();
        $count = 0;

        foreach ($students as $student) {
            $certificateNo = Certificate::generateCertificateNumber($request->type);
            $content = $this->generateContent($student, $request->type);
            $qrCode = $this->generateQRCode($certificateNo);

            Certificate::create([
                'student_id' => $student->id,
                'type' => $request->type,
                'certificate_no' => $certificateNo,
                'issue_date' => $request->issue_date,
                'content' => $content,
                'qr_code' => $qrCode,
                'issued_by' => auth()->guard('admin')->id()
            ]);

            $count++;
        }

        return redirect()
            ->route('admin.certificates.index')
            ->with('success', "Successfully generated {$count} certificates!");
    }

    // Public verification
    public function verify(Request $request)
    {
        if ($request->isMethod('post')) {
            $request->validate([
                'certificate_no' => 'required|string'
            ]);

            $certificate = Certificate::with('student')
                ->where('certificate_no', $request->certificate_no)
                ->where('status', 'active')
                ->first();

            if ($certificate) {
                return view('certificates.verify', compact('certificate'));
            } else {
                return back()->with('error', 'Certificate not found or has been cancelled.');
            }
        }

        return view('certificates.verify-form');
    }

    // Cancel certificate
    public function cancel($id)
    {
        $certificate = Certificate::findOrFail($id);
        $certificate->update(['status' => 'cancelled']);

        return back()->with('success', 'Certificate cancelled successfully!');
    }

    // Generate content based on type
    private function generateContent($student, $type)
    {
        $className = $student->class->class_name ?? 'N/A';
        
        $templates = [
            'bonafide' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, is a bonafide student of this institution studying in Class {$className} during the academic year " . date('Y') . "-" . (date('Y') + 1) . ".",
            
            'transfer' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, was a student of this institution in Class {$className}. The student has been granted transfer certificate on request. Date of Birth: {$student->date_of_birth}. The student's conduct and character were satisfactory during their stay in this institution.",
            
            'character' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, was a student of this institution in Class {$className}. During their stay in this institution, their conduct and character were found to be good. They are hardworking, sincere, and well-behaved.",
            
            'fee' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, Roll No: {$student->roll_number}, studying in Class {$className}, has paid all the fees due to the institution up to date. No dues are pending against the student.",
            
            'migration' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, was a student of this institution and has successfully completed Class {$className}. This certificate is issued to enable the student to seek admission in another institution. Date of Birth: {$student->date_of_birth}."
        ];

        return $templates[$type] ?? '';
    }

    // Generate QR Code
    private function generateQRCode($certificateNo)
    {
        try {
            if (class_exists('\SimpleSoftwareIO\QrCode\Facades\QrCode')) {
                $url = route('certificates.verify.form') . '?cert=' . $certificateNo;
                
                $qrCode = \SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')
                    ->size(150)
                    ->generate($url);
                
                return base64_encode($qrCode);
            }
        } catch (\Exception $e) {
            \Log::warning('QR Code generation failed: ' . $e->getMessage());
        }
        
        // Return empty string if QR code generation fails
        return '';
    }

    // Get students by class (AJAX)
    public function getStudentsByClass(Request $request)
    {
        $students = Student::where('class_id', $request->class_id)
            ->where('status', 'Active');

        if ($request->filled('section_id')) {
            $students->where('section_id', $request->section_id);
        }

        $students = $students->orderBy('first_name')->get();

        return response()->json([
            'success' => true,
            'students' => $students
        ]);
    }
}
