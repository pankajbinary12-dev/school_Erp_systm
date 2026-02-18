<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Checking Login System\n";
echo "====================\n\n";

// Check Admins
echo "ADMINS TABLE:\n";
$admins = App\Models\Admin::all();
echo "Total Admins: " . $admins->count() . "\n";
if ($admins->count() > 0) {
    foreach ($admins as $admin) {
        echo "  - Username: {$admin->username}, Email: {$admin->email}\n";
        echo "    Password Hash: " . substr($admin->password, 0, 20) . "...\n";
    }
} else {
    echo "  ⚠️ NO ADMINS FOUND!\n";
}

echo "\n";

// Check Students
echo "STUDENTS TABLE:\n";
$students = App\Models\Student::all();
echo "Total Students: " . $students->count() . "\n";
if ($students->count() > 0) {
    $student = $students->first();
    echo "  - First Student: {$student->first_name} {$student->last_name}\n";
    echo "    Admission No: {$student->admission_no}\n";
    if (isset($student->password)) {
        echo "    Has Password: Yes\n";
    } else {
        echo "    Has Password: No\n";
    }
}

echo "\n";

// Check Teachers
echo "TEACHERS TABLE:\n";
$teachers = App\Models\Teacher::all();
echo "Total Teachers: " . $teachers->count() . "\n";
if ($teachers->count() > 0) {
    $teacher = $teachers->first();
    echo "  - First Teacher: {$teacher->first_name} {$teacher->last_name}\n";
    echo "    Employee ID: {$teacher->employee_id}\n";
    if (isset($teacher->password)) {
        echo "    Has Password: Yes\n";
    } else {
        echo "    Has Password: No\n";
    }
}

echo "\n";

// Test password verification
echo "PASSWORD VERIFICATION TEST:\n";
if ($admins->count() > 0) {
    $admin = $admins->first();
    $testPasswords = ['admin123', 'password', '123456', 'admin'];
    
    foreach ($testPasswords as $pass) {
        if (Hash::check($pass, $admin->password)) {
            echo "  ✅ Admin password is: $pass\n";
            break;
        }
    }
}

echo "\n✅ Check Complete!\n";
