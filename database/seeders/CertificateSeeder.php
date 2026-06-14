<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Certificate;
use App\Models\Student;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class CertificateSeeder extends Seeder
{
    public function run(): void
    {
        // Get first 5 students
        $students = Student::where('status', 'Active')->take(5)->get();
        
        if ($students->isEmpty()) {
            $this->command->warn('No active students found. Please add students first.');
            return;
        }

        $types = ['bonafide', 'transfer', 'character', 'fee', 'migration'];
        
        foreach ($students as $index => $student) {
            $type = $types[$index % count($types)];
            $certificateNo = Certificate::generateCertificateNumber($type);
            
            // Generate content
            $content = $this->generateContent($student, $type);
            
            // Generate QR Code
            $url = route('certificates.verify.form') . '?cert=' . $certificateNo;
            $qrCode = QrCode::format('png')->size(150)->generate($url);
            
            Certificate::create([
                'student_id' => $student->id,
                'type' => $type,
                'certificate_no' => $certificateNo,
                'issue_date' => now()->subDays(rand(1, 30)),
                'content' => $content,
                'qr_code' => base64_encode($qrCode),
                'issued_by' => 1, // Assuming admin ID 1
                'status' => 'active'
            ]);
            
            $this->command->info("Created {$type} certificate for {$student->first_name} {$student->last_name}");
        }
        
        $this->command->info('Certificate seeding completed!');
    }
    
    private function generateContent($student, $type)
    {
        $templates = [
            'bonafide' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, is a bonafide student of this institution studying in Class {$student->class->name} during the academic year " . date('Y') . "-" . (date('Y') + 1) . ".",
            
            'transfer' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, was a student of this institution in Class {$student->class->name}. The student has been granted transfer certificate on request. Date of Birth: {$student->date_of_birth}. The student's conduct and character were satisfactory during their stay in this institution.",
            
            'character' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, was a student of this institution in Class {$student->class->name}. During their stay in this institution, their conduct and character were found to be good. They are hardworking, sincere, and well-behaved.",
            
            'fee' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, Roll No: {$student->roll_number}, studying in Class {$student->class->name}, has paid all the fees due to the institution up to date. No dues are pending against the student.",
            
            'migration' => "This is to certify that {$student->first_name} {$student->last_name}, son/daughter of {$student->father_name}, was a student of this institution and has successfully completed Class {$student->class->name}. This certificate is issued to enable the student to seek admission in another institution. Date of Birth: {$student->date_of_birth}."
        ];

        return $templates[$type] ?? '';
    }
}
