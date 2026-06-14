<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attendance_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->date('attendance_date');
            $table->time('start_time');
            $table->time('end_time')->nullable();
            $table->enum('status', ['Active', 'Completed'])->default('Active');
            $table->integer('total_students')->default(0);
            $table->integer('present_count')->default(0);
            $table->integer('absent_count')->default(0);
            $table->integer('late_count')->default(0);
            $table->integer('leave_count')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            // Prevent duplicate sessions
            $table->unique(['teacher_id', 'class_id', 'section_id', 'subject_id', 'attendance_date'], 'unique_session');
        });

        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('attendance_sessions')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('status', ['Present', 'Absent', 'Late', 'Leave'])->default('Absent');
            $table->time('marked_at')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            
            // One attendance record per student per session
            $table->unique(['session_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attendances');
        Schema::dropIfExists('attendance_sessions');
    }
};
