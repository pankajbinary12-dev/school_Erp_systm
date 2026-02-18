<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Session;
use App\Models\Classes;
use App\Models\Section;
use App\Models\Subject;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Admin;
use App\Models\User;
use App\Models\StaffMember;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create Default User
        User::create([
            'name' => 'Default User',
            'email' => 'user@school.com',
            'password' => Hash::make('password'),
            'email_verified_at' => now()
        ]);

        // Create Admin User
        Admin::create([
            'name' => 'Admin User',
            'email' => 'admin@school.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'phone' => '9999999999',
            'role' => 'super_admin',
            'is_active' => 'Active'
        ]);

        // Create Sessions
        $session = Session::create([
            'session_name' => '2024-2025',
            'start_date' => '2024-04-01',
            'end_date' => '2025-03-31',
            'is_active' => 'Active'
        ]);

        Session::create([
            'session_name' => '2023-2024',
            'start_date' => '2023-04-01',
            'end_date' => '2024-03-31',
            'is_active' => 'Inactive'
        ]);

        // Create Classes
        $classes = [];
        for ($i = 1; $i <= 12; $i++) {
            $classes[$i] = Classes::create([
                'class_name' => 'Class ' . $i,
                'class_numeric' => $i,
                'is_active' => 'Active'
            ]);
        }

        // Create Sections for each class
        $sections = ['A', 'B', 'C'];
        $sectionModels = [];
        
        foreach ($classes as $class) {
            foreach ($sections as $sectionName) {
                $sectionModels[] = Section::create([
                    'class_id' => $class->id,
                    'section_name' => $sectionName,
                    'capacity' => 40,
                    'is_active' => 'Active'
                ]);
            }
        }

        // Create Subjects
        $subjects = [
            ['subject_name' => 'Mathematics', 'subject_code' => 'MATH101'],
            ['subject_name' => 'Science', 'subject_code' => 'SCI101'],
            ['subject_name' => 'English', 'subject_code' => 'ENG101'],
            ['subject_name' => 'Hindi', 'subject_code' => 'HIN101'],
            ['subject_name' => 'Social Studies', 'subject_code' => 'SST101'],
            ['subject_name' => 'Computer Science', 'subject_code' => 'CS101'],
            ['subject_name' => 'Physics', 'subject_code' => 'PHY101'],
            ['subject_name' => 'Chemistry', 'subject_code' => 'CHEM101'],
            ['subject_name' => 'Biology', 'subject_code' => 'BIO101'],
            ['subject_name' => 'Physical Education', 'subject_code' => 'PE101'],
        ];

        foreach ($subjects as $subject) {
            Subject::create([
                'subject_name' => $subject['subject_name'],
                'subject_code' => $subject['subject_code'],
                'description' => 'Description for ' . $subject['subject_name'],
                'is_active' => 'Active'
            ]);
        }

        // Create Sample Students
        for ($i = 1; $i <= 10; $i++) {
            $classId = rand(1, 12);
            $sectionId = (($classId - 1) * 3) + rand(1, 3);
            
            Student::create([
                'admission_no' => 'STU' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'roll_no' => $i,
                'first_name' => 'Student' . $i,
                'last_name' => 'Kumar',
                'date_of_birth' => '2010-01-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'email' => 'student' . $i . '@school.com',
                'phone' => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Address ' . $i . ', City, State',
                'father_name' => 'Father ' . $i,
                'mother_name' => 'Mother ' . $i,
                'guardian_phone' => '98765432' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'class_id' => $classId,
                'section_id' => $sectionId,
                'session_id' => $session->id,
                'username' => 'student' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'status' => 'Active',
                'admission_date' => '2024-04-01'
            ]);
        }

        // Create Sample Teachers
        for ($i = 1; $i <= 5; $i++) {
            Teacher::create([
                'employee_id' => 'EMP' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name' => 'Teacher' . $i,
                'last_name' => 'Singh',
                'date_of_birth' => '1985-01-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'email' => 'teacher' . $i . '@school.com',
                'phone' => '87654321' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Teacher Address ' . $i . ', City, State',
                'qualification' => 'M.Ed, B.Ed',
                'joining_date' => '2020-04-01',
                'username' => 'teacher' . str_pad($i, 3, '0', STR_PAD_LEFT),
                'password' => Hash::make('password'),
                'status' => 'Active'
            ]);
        }

        // Create Sample Staff Members
        $designations = ['Accountant', 'Librarian', 'Lab Assistant', 'Clerk', 'Peon'];
        $departments = ['Administration', 'Library', 'Laboratory', 'Office', 'Maintenance'];
        
        for ($i = 1; $i <= 5; $i++) {
            StaffMember::create([
                'employee_id' => 'STAFF' . str_pad($i, 4, '0', STR_PAD_LEFT),
                'first_name' => 'Staff' . $i,
                'last_name' => 'Kumar',
                'date_of_birth' => '1988-06-' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'gender' => $i % 2 == 0 ? 'Male' : 'Female',
                'email' => 'staff' . $i . '@school.com',
                'phone' => '76543210' . str_pad($i, 2, '0', STR_PAD_LEFT),
                'address' => 'Staff Address ' . $i . ', City, State',
                'city' => 'City',
                'state' => 'State',
                'pin_code' => '110001',
                'qualification' => 'B.Com, Diploma',
                'designation' => $designations[$i - 1],
                'department' => $departments[$i - 1],
                'joining_date' => '2021-01-15',
                'salary' => 25000 + ($i * 2000),
                'status' => 'Active'
            ]);
        }

        echo "\n✅ Database seeded successfully!\n";
        echo "📚 Created:\n";
        echo "   - 1 Default User\n";
        echo "   - 1 Admin User\n";
        echo "   - 2 Sessions\n";
        echo "   - 12 Classes\n";
        echo "   - 36 Sections\n";
        echo "   - 10 Subjects\n";
        echo "   - 10 Students\n";
        echo "   - 5 Teachers\n";
        echo "   - 5 Staff Members\n\n";
        echo "🔐 Login Credentials:\n";
        echo "   User: email = user@school.com, password = password\n";
        echo "   Admin: username = admin, password = admin123\n";
        echo "   Student: username = student001, password = password\n";
        echo "   Teacher: username = teacher001, password = password\n";
        echo "   Staff: email = staff1@school.com\n\n";
    }
}
