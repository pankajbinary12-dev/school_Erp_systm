<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

$teachers = \App\Models\Teacher::select('id', 'employee_id', 'first_name', 'last_name', 'username', 'email', 'status')->get();

echo "Total Teachers: " . $teachers->count() . "\n\n";

if ($teachers->count() > 0) {
    echo "Teacher Login Credentials:\n";
    echo str_repeat("=", 80) . "\n";
    foreach ($teachers as $teacher) {
        echo "ID: {$teacher->id}\n";
        echo "Employee ID: {$teacher->employee_id}\n";
        echo "Name: {$teacher->first_name} {$teacher->last_name}\n";
        echo "Username: {$teacher->username}\n";
        echo "Email: {$teacher->email}\n";
        echo "Status: {$teacher->status}\n";
        echo "Default Password: password (or teacher123)\n";
        echo str_repeat("-", 80) . "\n";
    }
} else {
    echo "No teachers found in database!\n";
    echo "You need to add teachers first from Admin panel.\n";
}
