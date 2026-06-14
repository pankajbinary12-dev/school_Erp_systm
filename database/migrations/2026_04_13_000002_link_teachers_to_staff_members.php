<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Teacher;
use App\Models\StaffMember;

return new class extends Migration
{
    public function up(): void
    {
        // Link existing teachers to staff members by employee_id
        $teachers = Teacher::all();
        
        foreach ($teachers as $teacher) {
            // Find or create corresponding staff member
            $staff = StaffMember::where('employee_id', $teacher->employee_id)->first();
            
            if (!$staff) {
                // Create staff member from teacher data
                StaffMember::create([
                    'employee_id' => $teacher->employee_id,
                    'first_name' => $teacher->first_name,
                    'last_name' => $teacher->last_name,
                    'date_of_birth' => $teacher->date_of_birth,
                    'gender' => $teacher->gender,
                    'email' => $teacher->email,
                    'phone' => $teacher->phone,
                    'address' => $teacher->address ?? 'N/A',
                    'city' => 'N/A',
                    'state' => 'N/A',
                    'pin_code' => '000000',
                    'qualification' => $teacher->qualification,
                    'designation' => 'Teacher',
                    'department' => 'Academic',
                    'joining_date' => $teacher->joining_date,
                    'salary' => 35000.00,
                    'status' => $teacher->status
                ]);
            }
        }
        
        echo "\n✅ Teachers linked to staff members successfully!\n";
    }

    public function down(): void
    {
        // Optional: Remove staff members that were created from teachers
        // Be careful with this in production
    }
};
