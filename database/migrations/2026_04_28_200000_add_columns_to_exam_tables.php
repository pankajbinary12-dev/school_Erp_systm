<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Create exam_schedules table
        Schema::create('exam_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->decimal('max_marks', 8, 2);
            $table->decimal('practical_marks', 8, 2)->default(0);
            $table->decimal('passing_marks', 8, 2);
            $table->integer('exam_order')->nullable();
            $table->date('exam_date')->nullable();
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        // Create exam_marks table
        Schema::create('exam_marks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('exam_id')->constrained('exams')->onDelete('cascade');
            $table->foreignId('exam_schedule_id')->constrained('exam_schedules')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->decimal('theory_marks', 8, 2)->nullable();
            $table->decimal('practical_marks', 8, 2)->nullable();
            $table->decimal('total_marks', 8, 2)->nullable();
            $table->enum('status', ['present', 'absent'])->default('present');
            $table->boolean('is_passed')->default(false);
            $table->text('remarks')->nullable();
            $table->foreignId('entered_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->timestamp('entered_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('exam_marks');
        Schema::dropIfExists('exam_schedules');
    }
};
