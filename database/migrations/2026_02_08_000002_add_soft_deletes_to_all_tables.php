<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Add soft deletes to all main tables
        $tables = [
            'admins',
            'sessions',
            'classes',
            'sections',
            'subjects',
            'students',
            'teachers',
            'student_admissions',
            'staff_members',
            'staff_attendance',
            'staff_leaves',
            'exams',
            'exam_schedules',
            'exam_marks',
            'student_attendance',
            'books',
            'book_categories',
            'book_issues',
            'fee_categories',
            'fee_structures',
            'fee_collections',
            'users',
            'roles',
            'permissions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && !Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->softDeletes();
                });
            }
        }
    }

    public function down(): void
    {
        $tables = [
            'admins',
            'sessions',
            'classes',
            'sections',
            'subjects',
            'students',
            'teachers',
            'student_admissions',
            'staff_members',
            'staff_attendance',
            'staff_leaves',
            'exams',
            'exam_schedules',
            'exam_marks',
            'student_attendance',
            'books',
            'book_categories',
            'book_issues',
            'fee_categories',
            'fee_structures',
            'fee_collections',
            'users',
            'roles',
            'permissions'
        ];

        foreach ($tables as $table) {
            if (Schema::hasTable($table) && Schema::hasColumn($table, 'deleted_at')) {
                Schema::table($table, function (Blueprint $table) {
                    $table->dropSoftDeletes();
                });
            }
        }
    }
};
