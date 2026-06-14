<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('staff_attendance', function (Blueprint $table) {
            // Add working hours calculation
            $table->decimal('working_hours', 5, 2)->nullable()->after('check_out');
            
            // Add marked by admin tracking
            $table->unsignedBigInteger('marked_by')->nullable()->after('remarks');
            
            // Add late arrival tracking
            $table->time('expected_check_in')->default('09:00:00')->after('check_in');
            $table->boolean('is_late')->default(false)->after('status');
            
            // Add index for faster queries
            $table->index(['staff_id', 'attendance_date']);
            $table->index('attendance_date');
        });
    }

    public function down(): void
    {
        Schema::table('staff_attendance', function (Blueprint $table) {
            $table->dropIndex(['staff_id', 'attendance_date']);
            $table->dropIndex(['attendance_date']);
            $table->dropColumn([
                'working_hours',
                'marked_by',
                'expected_check_in',
                'is_late'
            ]);
        });
    }
};
