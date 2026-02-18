<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Permissions
        $permissions = [
            // Student Management
            ['name' => 'view_students', 'display_name' => 'View Students', 'module' => 'students'],
            ['name' => 'create_students', 'display_name' => 'Create Students', 'module' => 'students'],
            ['name' => 'edit_students', 'display_name' => 'Edit Students', 'module' => 'students'],
            ['name' => 'delete_students', 'display_name' => 'Delete Students', 'module' => 'students'],
            
            // Teacher Management
            ['name' => 'view_teachers', 'display_name' => 'View Teachers', 'module' => 'teachers'],
            ['name' => 'create_teachers', 'display_name' => 'Create Teachers', 'module' => 'teachers'],
            ['name' => 'edit_teachers', 'display_name' => 'Edit Teachers', 'module' => 'teachers'],
            ['name' => 'delete_teachers', 'display_name' => 'Delete Teachers', 'module' => 'teachers'],
            
            // Staff Management
            ['name' => 'view_staff', 'display_name' => 'View Staff', 'module' => 'staff'],
            ['name' => 'create_staff', 'display_name' => 'Create Staff', 'module' => 'staff'],
            ['name' => 'edit_staff', 'display_name' => 'Edit Staff', 'module' => 'staff'],
            ['name' => 'delete_staff', 'display_name' => 'Delete Staff', 'module' => 'staff'],
            
            // Attendance Management
            ['name' => 'view_attendance', 'display_name' => 'View Attendance', 'module' => 'attendance'],
            ['name' => 'mark_attendance', 'display_name' => 'Mark Attendance', 'module' => 'attendance'],
            
            // Exam Management
            ['name' => 'view_exams', 'display_name' => 'View Exams', 'module' => 'exams'],
            ['name' => 'create_exams', 'display_name' => 'Create Exams', 'module' => 'exams'],
            ['name' => 'edit_exams', 'display_name' => 'Edit Exams', 'module' => 'exams'],
            ['name' => 'delete_exams', 'display_name' => 'Delete Exams', 'module' => 'exams'],
            ['name' => 'enter_marks', 'display_name' => 'Enter Marks', 'module' => 'exams'],
            
            // Fee Management
            ['name' => 'view_fees', 'display_name' => 'View Fees', 'module' => 'fees'],
            ['name' => 'collect_fees', 'display_name' => 'Collect Fees', 'module' => 'fees'],
            ['name' => 'manage_fee_structure', 'display_name' => 'Manage Fee Structure', 'module' => 'fees'],
            
            // Library Management
            ['name' => 'view_library', 'display_name' => 'View Library', 'module' => 'library'],
            ['name' => 'manage_books', 'display_name' => 'Manage Books', 'module' => 'library'],
            ['name' => 'issue_books', 'display_name' => 'Issue Books', 'module' => 'library'],
            
            // Academic Management
            ['name' => 'manage_classes', 'display_name' => 'Manage Classes', 'module' => 'academic'],
            ['name' => 'manage_subjects', 'display_name' => 'Manage Subjects', 'module' => 'academic'],
            ['name' => 'manage_timetable', 'display_name' => 'Manage Timetable', 'module' => 'academic'],
            
            // Reports
            ['name' => 'view_reports', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'generate_reports', 'display_name' => 'Generate Reports', 'module' => 'reports'],
            
            // Settings
            ['name' => 'manage_settings', 'display_name' => 'Manage Settings', 'module' => 'settings'],
            ['name' => 'manage_users', 'display_name' => 'Manage Users', 'module' => 'settings'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                $permission
            );
        }

        // Create Roles
        $superAdmin = Role::firstOrCreate(
            ['name' => 'super_admin'],
            [
                'display_name' => 'Super Admin',
                'description' => 'Full system access with all permissions'
            ]
        );

        $admin = Role::firstOrCreate(
            ['name' => 'admin'],
            [
                'display_name' => 'Admin',
                'description' => 'Administrative access with most permissions'
            ]
        );

        $teacher = Role::firstOrCreate(
            ['name' => 'teacher'],
            [
                'display_name' => 'Teacher',
                'description' => 'Teacher access for class management and attendance'
            ]
        );

        $accountant = Role::firstOrCreate(
            ['name' => 'accountant'],
            [
                'display_name' => 'Accountant',
                'description' => 'Fee collection and financial management'
            ]
        );

        $librarian = Role::firstOrCreate(
            ['name' => 'librarian'],
            [
                'display_name' => 'Librarian',
                'description' => 'Library management and book issuing'
            ]
        );

        // Assign all permissions to Super Admin
        $superAdmin->permissions()->sync(Permission::all());

        // Assign specific permissions to Admin
        $adminPermissions = Permission::whereIn('module', [
            'students', 'teachers', 'staff', 'attendance', 'exams', 'academic', 'reports'
        ])->pluck('id');
        $admin->permissions()->sync($adminPermissions);

        // Assign specific permissions to Teacher
        $teacherPermissions = Permission::whereIn('name', [
            'view_students', 'view_attendance', 'mark_attendance', 
            'view_exams', 'enter_marks', 'view_reports'
        ])->pluck('id');
        $teacher->permissions()->sync($teacherPermissions);

        // Assign specific permissions to Accountant
        $accountantPermissions = Permission::where('module', 'fees')->pluck('id');
        $accountant->permissions()->sync($accountantPermissions);

        // Assign specific permissions to Librarian
        $librarianPermissions = Permission::where('module', 'library')->pluck('id');
        $librarian->permissions()->sync($librarianPermissions);

        echo "\n✅ Roles and Permissions seeded successfully!\n";
        echo "📋 Created:\n";
        echo "   - " . Permission::count() . " Permissions\n";
        echo "   - " . Role::count() . " Roles\n\n";
        echo "🔐 Roles:\n";
        echo "   - Super Admin (All Permissions)\n";
        echo "   - Admin (Most Permissions)\n";
        echo "   - Teacher (Limited Permissions)\n";
        echo "   - Accountant (Fee Management)\n";
        echo "   - Librarian (Library Management)\n\n";
    }
}
