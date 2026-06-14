<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_notifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->string('title');
            $table->text('message');
            $table->enum('type', ['info', 'warning', 'success', 'danger'])->default('info');
            $table->enum('category', ['assignment', 'attendance', 'exam', 'fee', 'general'])->default('general');
            $table->boolean('is_read')->default(false);
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'is_read']);
            $table->index('created_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_notifications');
    }
};
