<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class GradeSystemSeeder extends Seeder
{
    public function run(): void
    {
        $grades = [
            ['grade' => 'A+', 'min_percentage' => 90, 'max_percentage' => 100, 'grade_point' => '10', 'description' => 'Outstanding', 'status' => 'active'],
            ['grade' => 'A', 'min_percentage' => 80, 'max_percentage' => 89.99, 'grade_point' => '9', 'description' => 'Excellent', 'status' => 'active'],
            ['grade' => 'B+', 'min_percentage' => 70, 'max_percentage' => 79.99, 'grade_point' => '8', 'description' => 'Very Good', 'status' => 'active'],
            ['grade' => 'B', 'min_percentage' => 60, 'max_percentage' => 69.99, 'grade_point' => '7', 'description' => 'Good', 'status' => 'active'],
            ['grade' => 'C', 'min_percentage' => 50, 'max_percentage' => 59.99, 'grade_point' => '6', 'description' => 'Average', 'status' => 'active'],
            ['grade' => 'D', 'min_percentage' => 33, 'max_percentage' => 49.99, 'grade_point' => '5', 'description' => 'Pass', 'status' => 'active'],
            ['grade' => 'F', 'min_percentage' => 0, 'max_percentage' => 32.99, 'grade_point' => '0', 'description' => 'Fail', 'status' => 'active'],
        ];

        foreach ($grades as $grade) {
            DB::table('grade_systems')->updateOrInsert(
                ['grade' => $grade['grade']],
                array_merge($grade, [
                    'created_at' => now(),
                    'updated_at' => now()
                ])
            );
        }
    }
}
