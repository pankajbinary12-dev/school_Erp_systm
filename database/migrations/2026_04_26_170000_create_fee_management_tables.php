<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Fee Types Table
        Schema::create('fee_types', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Tuition, Transport, Exam, etc.
            $table->string('code')->unique();
            $table->text('description')->nullable();
            $table->enum('frequency', ['monthly', 'quarterly', 'half_yearly', 'yearly', 'one_time'])->default('monthly');
            $table->boolean('is_mandatory')->default(true);
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Fee Structures Table (Class-wise fee structure)
        Schema::create('fee_structures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('fee_type_id')->constrained('fee_types')->onDelete('cascade');
            $table->foreignId('session_id')->nullable()->constrained('sessions')->onDelete('set null');
            $table->decimal('amount', 10, 2);
            $table->date('due_date')->nullable();
            $table->decimal('late_fee_amount', 10, 2)->default(0);
            $table->integer('late_fee_days')->default(0); // Days after which late fee applies
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['class_id', 'fee_type_id', 'session_id']);
        });

        // Student Fees Table (Assigned fees to students)
        Schema::create('student_fees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('fee_structure_id')->constrained('fee_structures')->onDelete('cascade');
            $table->decimal('total_amount', 10, 2);
            $table->decimal('discount_amount', 10, 2)->default(0);
            $table->string('discount_reason')->nullable();
            $table->decimal('paid_amount', 10, 2)->default(0);
            $table->decimal('due_amount', 10, 2);
            $table->decimal('late_fee', 10, 2)->default(0);
            $table->date('due_date')->nullable();
            $table->enum('status', ['pending', 'partial', 'paid', 'overdue'])->default('pending');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'status']);
        });

        // Fee Payments Table
        Schema::create('fee_payments', function (Blueprint $table) {
            $table->id();
            $table->string('receipt_no')->unique();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('student_fee_id')->constrained('student_fees')->onDelete('cascade');
            $table->decimal('amount', 10, 2);
            $table->decimal('late_fee_paid', 10, 2)->default(0);
            $table->enum('payment_mode', ['cash', 'upi', 'card', 'cheque', 'bank_transfer', 'online'])->default('cash');
            $table->string('transaction_id')->nullable();
            $table->string('cheque_no')->nullable();
            $table->date('cheque_date')->nullable();
            $table->string('bank_name')->nullable();
            $table->text('remarks')->nullable();
            $table->date('payment_date');
            $table->foreignId('collected_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->enum('status', ['success', 'pending', 'failed', 'refunded'])->default('success');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'payment_date']);
            $table->index('receipt_no');
        });

        // Fee Discounts Table
        Schema::create('fee_discounts', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->enum('type', ['percentage', 'fixed'])->default('percentage');
            $table->decimal('value', 10, 2);
            $table->text('description')->nullable();
            $table->date('valid_from')->nullable();
            $table->date('valid_to')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Fee Reminders Table
        Schema::create('fee_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('student_fee_id')->constrained('student_fees')->onDelete('cascade');
            $table->enum('type', ['sms', 'email', 'notification'])->default('notification');
            $table->text('message');
            $table->enum('status', ['sent', 'pending', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_reminders');
        Schema::dropIfExists('fee_discounts');
        Schema::dropIfExists('fee_payments');
        Schema::dropIfExists('student_fees');
        Schema::dropIfExists('fee_structures');
        Schema::dropIfExists('fee_types');
    }
};
