<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_admissions', function (Blueprint $table) {
            $table->id();
            
            // Admission Number (Auto-generated)
            $table->string('admission_no', 20)->unique();
            
            // Student Basic Information
            $table->string('student_name');
            $table->date('dob');
            $table->string('gender', 20);
            $table->foreignId('class_id')->nullable()->constrained('classes')->onDelete('set null');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('set null');
            $table->string('student_email')->unique();
            $table->string('blood_group', 10)->nullable();
            $table->string('religion', 50)->nullable();
            $table->string('caste', 50)->nullable();
            $table->string('nationality', 50)->nullable();
            
            // Address Information
            $table->text('stu_address')->nullable();
            $table->text('permanent_address')->nullable();
            $table->string('city_name', 100)->nullable();
            $table->string('state_name', 100)->nullable();
            $table->string('pin_code', 20)->nullable();
            $table->string('phone', 15)->nullable();
            
            // Admission Information
            $table->date('admission_date');
            $table->string('previous_school')->nullable();
            $table->string('previous_school_name')->nullable();
            $table->string('previous_class', 100)->nullable();
            $table->string('tc_number', 100)->nullable();
            $table->boolean('status')->default(true);
            
            // Father Information
            $table->string('father_name');
            $table->string('father_occupation', 100)->nullable();
            $table->string('father_phone', 15);
            $table->string('father_email')->nullable();
            $table->string('father_photo')->nullable();
            
            // Mother Information
            $table->string('mother_name');
            $table->string('mother_phone', 15);
            $table->string('mother_occupation', 100)->nullable();
            $table->string('mother_email')->nullable();
            $table->string('mother_photo')->nullable();
            
            // Guardian Information
            $table->string('guardian_name')->nullable();
            $table->string('guardian_phone', 15)->nullable();
            $table->string('guardian_email')->nullable();
            $table->string('relation', 100)->nullable();
            
            // Emergency Contact
            $table->string('emergency_contact', 15)->nullable();
            $table->string('contact_phone', 15)->nullable();
            
            // Documents
            $table->string('student_photo')->nullable();
            $table->string('birth_certificate')->nullable();
            $table->string('aadhar_card_front')->nullable();
            $table->string('aadhar_card_back')->nullable();
            
            // Medical Information
            $table->text('medical_info')->nullable();
            $table->text('allergies')->nullable();
            
            $table->softDeletes();
            $table->timestamps();
            
            // Indexes
            $table->index('admission_no');
            $table->index('student_email');
            $table->index('admission_date');
            $table->index(['class_id', 'section_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_admissions');
    }
};
