<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Updating Admission Numbers ===\n\n";

// Get all student admissions without admission_no
$admissions = App\Models\StudentAdmission::whereNull('admission_no')
    ->orWhere('admission_no', '')
    ->orderBy('admission_date')
    ->orderBy('id')
    ->get();

echo "Found {$admissions->count()} admissions without admission number\n\n";

$updated = 0;
$yearCounters = [];

foreach ($admissions as $admission) {
    // Get year from admission date
    $year = date('Y', strtotime($admission->admission_date));
    
    // Initialize counter for this year if not exists
    if (!isset($yearCounters[$year])) {
        // Check if there are existing admission numbers for this year
        $lastAdmission = App\Models\StudentAdmission::where('admission_no', 'LIKE', $year . '%')
            ->orderBy('admission_no', 'desc')
            ->first();
        
        if ($lastAdmission) {
            $lastNumber = intval(substr($lastAdmission->admission_no, 4));
            $yearCounters[$year] = $lastNumber;
        } else {
            $yearCounters[$year] = 0;
        }
    }
    
    // Increment counter
    $yearCounters[$year]++;
    
    // Generate admission number
    $admissionNo = $year . str_pad($yearCounters[$year], 4, '0', STR_PAD_LEFT);
    
    // Update record
    $admission->admission_no = $admissionNo;
    $admission->save();
    
    echo "Updated ID {$admission->id}: {$admission->student_name} → {$admissionNo}\n";
    $updated++;
}

echo "\n✅ Updated {$updated} admission numbers successfully!\n";
