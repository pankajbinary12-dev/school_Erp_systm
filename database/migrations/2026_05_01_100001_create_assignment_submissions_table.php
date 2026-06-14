<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Check if table already exists
        if (!Schema::hasTable('assignment_submissions')) {
            Schema::create('assignment_submissions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('assignment_id')->constrained('assignments')->onDelete('cascade');
                $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
                $table->text('submission_text')->nullable();
                $table->string('file_path')->nullable();
                $table->timestamp('submitted_at')->nullable();
                $table->enum('status', ['pending', 'submitted', 'late', 'graded'])->default('pending');
                $table->decimal('marks_obtained', 5, 2)->nullable();
                $table->text('teacher_feedback')->nullable();
                $table->timestamps();
                $table->softDeletes();
                
                $table->index(['assignment_id', 'student_id']);
                $table->index('status');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('assignment_submissions');
    }
};
