# Permissions & Roles Guide

## Overview
School ERP System ab complete Role-Based Access Control (RBAC) system ke saath hai.

## Database Tables

### 1. **roles** - User roles store karta hai
- id
- name (unique)
- display_name
- description
- is_active
- timestamps

### 2. **permissions** - System permissions store karta hai
- id
- name (unique)
- display_name
- description
- module (students, teachers, fees, etc.)
- is_active
- timestamps

### 3. **role_permissions** - Role aur Permission ka mapping
- role_id
- permission_id

### 4. **Pivot Tables** - Different user types ke liye
- user_roles (users table ke liye)
- admin_roles (admins table ke liye)
- teacher_roles (teachers table ke liye)
- staff_roles (staff_members table ke liye)

## Default Roles

### 1. Super Admin
- **Full system access**
- All permissions
- Can manage everything

### 2. Admin
- Most administrative permissions
- Cannot manage system settings
- Modules: students, teachers, staff, attendance, exams, academic, reports

### 3. Teacher
- Limited classroom permissions
- Permissions:
  - view_students
  - view_attendance, mark_attendance
  - view_exams, enter_marks
  - view_reports

### 4. Accountant
- Fee management only
- All fee-related permissions

### 5. Librarian
- Library management only
- All library-related permissions

## Permissions by Module

### Students Module
- view_students
- create_students
- edit_students
- delete_students

### Teachers Module
- view_teachers
- create_teachers
- edit_teachers
- delete_teachers

### Staff Module
- view_staff
- create_staff
- edit_staff
- delete_staff

### Attendance Module
- view_attendance
- mark_attendance

### Exams Module
- view_exams
- create_exams
- edit_exams
- delete_exams
- enter_marks

### Fees Module
- view_fees
- collect_fees
- manage_fee_structure

### Library Module
- view_library
- manage_books
- issue_books

### Academic Module
- manage_classes
- manage_subjects
- manage_timetable

### Reports Module
- view_reports
- generate_reports

### Settings Module
- manage_settings
- manage_users

## Usage Examples

### Model mein Trait use karna

```php
use App\Traits\HasRolesAndPermissions;

class Admin extends Authenticatable
{
    use HasRolesAndPermissions;
    
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'admin_roles');
    }
}
```

### Role Assign karna

```php
$admin = Admin::find(1);
$admin->assignRole('super_admin');
// or
$admin->assignRole(Role::find(1));
```

### Permission Check karna

```php
// Single permission check
if ($admin->hasPermission('create_students')) {
    // Allow action
}

// Multiple permissions check (any)
if ($admin->hasAnyPermission(['create_students', 'edit_students'])) {
    // Allow action
}

// Role check
if ($admin->hasRole('super_admin')) {
    // Allow action
}
```

### Controller mein use karna

```php
public function store(Request $request)
{
    if (!auth()->user()->hasPermission('create_students')) {
        abort(403, 'Unauthorized action.');
    }
    
    // Create student logic
}
```

### Blade Template mein use karna

```blade
@if(auth()->user()->hasPermission('create_students'))
    <a href="{{ route('students.create') }}" class="btn btn-primary">
        Add Student
    </a>
@endif
```

### Middleware create karna (Optional)

```php
// app/Http/Middleware/CheckPermission.php
public function handle($request, Closure $next, $permission)
{
    if (!auth()->user()->hasPermission($permission)) {
        abort(403);
    }
    return $next($request);
}

// Route mein use
Route::post('/students', [StudentController::class, 'store'])
    ->middleware('permission:create_students');
```

## Seeder Run karna

```bash
# Only permissions and roles seed karna
php artisan db:seed --class=RolesAndPermissionsSeeder

# Fresh database with all data
php artisan migrate:fresh --seed
```

## Notes

- Super Admin ko automatically all permissions milte hain
- Roles aur permissions runtime mein bhi add/remove kar sakte hain
- Har user type (Admin, Teacher, Staff) ke liye alag pivot table hai
- Permissions module-wise organized hain for better management
