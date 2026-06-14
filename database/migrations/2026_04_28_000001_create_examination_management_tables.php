<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Exam Marks Table (Enhanced existing table)
        if (!Schema::hasTable('exam_marks')) {
            Schema::create('exam_marks', function (Blueprint $table) {
                $table->id();
                $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
                $table->decimal('marks_obtained', 5, 2);
                $table->decimal('max_marks', 5, 2);
                $table->enum('status', ['present', 'absent'])->default('present');
                $table->text('remarks')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->unique(['exam_schedule_id', 'student_id', 'subject_id']);
            });
        }

        // Student Results Table
        Schema::create('student_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->decimal('total_marks_obtained', 8, 2);
            $table->decimal('total_max_marks', 8, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade', 10);
            $table->enum('result', ['pass', 'fail'])->default('fail');
            $table->integer('rank')->nullable();
            $table->integer('total_students')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->unique(['student_id', 'exam_id']);
        });

        // Grade System Table
        Schema::create('grade_systems', function (Blueprint $table) {
            $table->id();
            $table->string('grade', 10);
            $table->decimal('min_percentage', 5, 2);
            $table->decimal('max_percentage', 5, 2);
            $table->string('grade_point', 10)->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Subject Wise Results Table
        Schema::create('subject_wise_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_result_id')->constrained('student_results')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->decimal('marks_obtained', 5, 2);
            $table->decimal('max_marks', 5, 2);
            $table->decimal('percentage', 5, 2);
            $table->string('grade', 10);
            $table->enum('status', ['pass', 'fail'])->default('fail');
            $table->timestamps();
        });

        // Performance Analytics Table
        Schema::create('performance_analytics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->decimal('average_marks', 5, 2);
            $table->decimal('highest_marks', 5, 2);
            $table->decimal('lowest_marks', 5, 2);
            $table->integer('subject_rank')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('performance_analytics');
        Schema::dropIfExists('subject_wise_results');
        Schema::dropIfExists('grade_systems');
        Schema::dropIfExists('student_results');
        // Don't drop exam_marks if it already exists
    }
};
