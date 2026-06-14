<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('enquiries', function (Blueprint $table) {
            $table->id();
            $table->string('enquiry_number')->unique(); // ENQ/2026/0001
            $table->date('enquiry_date');
            
            // Student Basic Details
            $table->string('first_name');
            $table->string('last_name');
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Other']);
            $table->string('email')->nullable();
            $table->string('phone');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            
            // Academic Details
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('session_id')->constrained('sessions')->onDelete('cascade');
            $table->string('previous_school')->nullable();
            $table->string('previous_class')->nullable();
            $table->decimal('previous_percentage', 5, 2)->nullable();
            
            // Parent/Guardian Details
            $table->string('father_name');
            $table->string('father_phone');
            $table->string('father_occupation')->nullable();
            $table->string('mother_name');
            $table->string('mother_phone')->nullable();
            $table->string('mother_occupation')->nullable();
            $table->decimal('annual_income', 10, 2)->nullable();
            
            // Enquiry Status
            $table->enum('status', ['Pending', 'Approved', 'Rejected', 'Converted'])->default('Pending');
            $table->text('remarks')->nullable();
            $table->string('source')->nullable(); // Walk-in, Phone, Online, Reference
            $table->string('reference_by')->nullable();
            
            // Follow-up
            $table->date('follow_up_date')->nullable();
            $table->text('follow_up_notes')->nullable();
            
            // Fee Payment (after approval)
            $table->decimal('registration_fee', 10, 2)->default(0);
            $table->enum('fee_status', ['Pending', 'Paid', 'Partial'])->default('Pending');
            $table->decimal('fee_paid', 10, 2)->default(0);
            $table->date('fee_paid_date')->nullable();
            $table->string('payment_mode')->nullable(); // Cash, Online, Cheque
            $table->string('transaction_id')->nullable();
            
            // Admission (after fee payment)
            $table->foreignId('student_id')->nullable()->constrained('students')->onDelete('set null');
            $table->string('admission_number')->nullable();
            $table->date('admission_date')->nullable();
            
            // Tracking
            $table->foreignId('created_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->foreignId('approved_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('approved_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('enquiries');
    }
};
