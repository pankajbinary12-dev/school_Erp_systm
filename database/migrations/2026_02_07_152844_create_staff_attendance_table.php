<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('staff_attendance', function (Blueprint $table) {
          $table->id();
          $table->unsignedBigInteger('staff_id');
          $table->date('attendance_date');
          $table->enum('status', ['Present', 'Absent', 'Half Day', 'Late', 'On Leave'])->default('Present');
          $table->time('check_in')->nullable();
          $table->time('check_out')->nullable();
          $table->text('remarks')->nullable();
          $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff_attendance');
    }
};
