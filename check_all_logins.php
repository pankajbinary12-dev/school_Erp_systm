<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== TEACHERS TABLE ===\n";
$teachers = \App\Models\Teacher::select('id', 'employee_id', 'first_name', 'last_name', 'username', 'email')->get();
echo "Total Teachers: " . $teachers->count() . "\n\n";
if ($teachers->count() > 0) {
    foreach ($teachers as $t) {
        echo "Username: {$t->username} | Name: {$t->first_name} {$t->last_name}\n";
    }
}

echo "\n=== STAFF_MEMBERS TABLE ===\n";
$staff = \App\Models\StaffMember::select('id', 'employee_id', 'first_name', 'last_name', 'username', 'email')->get();
echo "Total Staff: " . $staff->count() . "\n\n";
if ($staff->count() > 0) {
    foreach ($staff as $s) {
        echo "Username: {$s->username} | Name: {$s->first_name} {$s->last_name}\n";
    }
}

echo "\n=== ADMINS TABLE ===\n";
$admins = \App\Models\Admin::select('id', 'username', 'email')->get();
echo "Total Admins: " . $admins->count() . "\n\n";
if ($admins->count() > 0) {
    foreach ($admins as $a) {
        echo "Username: {$a->username} | Email: {$a->email}\n";
    }
}

echo "\n=== STUDENTS TABLE ===\n";
$students = \App\Models\Student::select('id', 'admission_number', 'first_name', 'last_name', 'username')->limit(5)->get();
echo "Total Students (showing first 5): " . \App\Models\Student::count() . "\n\n";
if ($students->count() > 0) {
    foreach ($students as $s) {
        echo "Username: {$s->username} | Name: {$s->first_name} {$s->last_name}\n";
    }
}
