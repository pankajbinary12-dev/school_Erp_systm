<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('timetables', function (Blueprint $table) {
            $table->id();
            $table->foreignId('class_id')->constrained('classes')->onDelete('cascade');
            $table->foreignId('section_id')->constrained('sections')->onDelete('cascade');
            $table->foreignId('subject_id')->constrained('subjects')->onDelete('cascade');
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->enum('day', ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday']);
            $table->time('start_time');
            $table->time('end_time');
            $table->string('room_number')->nullable();
            $table->enum('is_active', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['class_id', 'section_id', 'day']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('timetables');
    }
};
