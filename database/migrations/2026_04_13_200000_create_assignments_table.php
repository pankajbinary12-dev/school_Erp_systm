<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->nullable()->constrained('sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->string('title');
            $table->text('description');
            $table->date('assigned_date');
            $table->date('due_date');
            $table->integer('total_marks')->default(100);
            $table->enum('status', ['Active', 'Completed', 'Cancelled'])->default('Active');
            $table->string('attachment')->nullable();
            $table->text('instructions')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('assignment_submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->text('submission_text')->nullable();
            $table->string('attachment')->nullable();
            $table->dateTime('submitted_at')->nullable();
            $table->integer('marks_obtained')->nullable();
            $table->enum('status', ['Pending', 'Submitted', 'Graded', 'Late'])->default('Pending');
            $table->text('teacher_feedback')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
        Schema::dropIfExists('assignments');
    }
};
