<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Teacher Roles table
        Schema::create('teacher_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('teacher_id')->constrained('teachers')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['teacher_id', 'role_id']);
        });

        // Staff Roles table
        Schema::create('staff_roles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('staff_id')->constrained('staff_members')->onDelete('cascade');
            $table->foreignId('role_id')->constrained('roles')->onDelete('cascade');
            $table->timestamps();
            
            $table->unique(['staff_id', 'role_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('teacher_roles');
        Schema::dropIfExists('staff_roles');
    }
};
