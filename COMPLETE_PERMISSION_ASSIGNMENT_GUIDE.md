# Complete Permission Assignment Guide

## Scenario: Teacher ko Edit, Delete, View ka access dena hai

### Step-by-Step Process

---

## Method 1: Using Frontend (Recommended)

### Step 1: Permissions Check Karo
1. Go to: **Settings → Permissions Management**
2. Check karo ki ye permissions exist karte hain:
   - `view_students`
   - `edit_students`
   - `delete_students`
3. Agar nahi hain toh create karo

---

### Step 2: Role Ko Permissions Assign Karo

#### Option A: Existing "Teacher" Role Use Karo
1. Go to: **Settings → Assign Permissions**
2. Left panel se **"Teacher"** role select karo
3. Right panel mein permissions dikhenge (module-wise)
4. Students module mein check karo:
   - ☑ View Students
   - ☑ Edit Students
   - ☑ Delete Students
5. Click **"Save Permissions"**
6. Success message dikhega

#### Option B: New Role Create Karo
1. Go to: **Settings → Roles Management**
2. Click **"Add New Role"**
3. Fill form:
   - Name: `senior_teacher`
   - Display Name: `Senior Teacher`
   - Description: `Teacher with edit and delete access`
4. Click **"Save Role"**
5. Now go to **Settings → Assign Permissions**
6. Select "Senior Teacher" role
7. Assign required permissions
8. Save

---

### Step 3: Teacher Ko Role Assign Karo

1. Go to: **Settings → Assign Roles to Users**
2. **Teachers** section mein jao
3. Teacher dhundo (e.g., "Ramesh Kumar")
4. Click **"Assign Roles"** button
5. Modal open hoga
6. Check karo:
   - ☑ Teacher (or Senior Teacher)
7. Click **"Save Roles"**
8. Success message dikhega
9. "Current Roles" column mein badge dikhega

---

### Step 4: Verify Karo

1. Same page par "Current Roles" column check karo
2. Teacher ke saamne role badge dikhna chahiye
3. Ab wo teacher login kare
4. Use edit aur delete buttons dikhne chahiye

---

## Method 2: Using Code (For Developers)

### In Controller (Example: StudentController)

```php
use App\Traits\HasRolesAndPermissions;

class StudentController extends Controller
{
    public function index()
    {
        // Check if user has permission
        if (!auth()->user()->hasPermission('view_students')) {
            abort(403, 'Unauthorized action.');
        }
        
        $students = Student::all();
        return view('students.index', compact('students'));
    }
    
    public function edit($id)
    {
        if (!auth()->user()->hasPermission('edit_students')) {
            abort(403, 'Unauthorized action.');
        }
        
        $student = Student::findOrFail($id);
        return view('students.edit', compact('student'));
    }
    
    public function destroy($id)
    {
        if (!auth()->user()->hasPermission('delete_students')) {
            abort(403, 'Unauthorized action.');
        }
        
        Student::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Student deleted!');
    }
}
```

### In Blade Template

```blade
{{-- Show edit button only if user has permission --}}
@if(auth()->user()->hasPermission('edit_students'))
    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning">
        <i class="fas fa-edit"></i> Edit
    </a>
@endif

{{-- Show delete button only if user has permission --}}
@if(auth()->user()->hasPermission('delete_students'))
    <button onclick="deleteStudent({{ $student->id }})" class="btn btn-danger">
        <i class="fas fa-trash"></i> Delete
    </button>
@endif

{{-- Show view button (usually everyone can view) --}}
@if(auth()->user()->hasPermission('view_students'))
    <a href="{{ route('students.show', $student->id) }}" class="btn btn-info">
        <i class="fas fa-eye"></i> View
    </a>
@endif
```

### Using Middleware

```php
// In routes/web.php
Route::middleware(['auth:admin', 'permission:view_students'])->group(function() {
    Route::get('/students', [StudentController::class, 'index']);
});

Route::middleware(['auth:admin', 'permission:edit_students'])->group(function() {
    Route::get('/students/{id}/edit', [StudentController::class, 'edit']);
    Route::put('/students/{id}', [StudentController::class, 'update']);
});

Route::middleware(['auth:admin', 'permission:delete_students'])->group(function() {
    Route::delete('/students/{id}', [StudentController::class, 'destroy']);
});
```

---

## Real-World Examples

### Example 1: Principal Ko Full Access

**Goal:** Principal ko sab kuch karne ka access

**Steps:**
1. Settings → Roles Management
2. Check if "Principal" role exists, if not create it
3. Settings → Assign Permissions
4. Select "Principal" role
5. Click "Select All Permissions"
6. Save
7. Settings → Assign Roles to Users
8. Find principal in Admins section
9. Assign "Principal" role
10. Done!

---

### Example 2: Class Teacher Ko Limited Access

**Goal:** Class teacher ko sirf apni class ke students edit karne ka access

**Steps:**
1. Settings → Roles Management
2. Create "Class Teacher" role
3. Settings → Assign Permissions
4. Select "Class Teacher" role
5. Assign:
   - ☑ view_students
   - ☑ edit_students
   - ☑ mark_attendance
   - ☑ enter_marks
6. Save
7. Settings → Assign Roles to Users
8. Find teacher
9. Assign "Class Teacher" role
10. Done!

---

### Example 3: Office Staff Ko View Only Access

**Goal:** Office staff ko sirf dekhne ka access, edit/delete nahi

**Steps:**
1. Settings → Roles Management
2. Create "Office Staff" role
3. Settings → Assign Permissions
4. Select "Office Staff" role
5. Assign only view permissions:
   - ☑ view_students
   - ☑ view_teachers
   - ☑ view_attendance
   - ☑ view_fees
6. Save
7. Settings → Assign Roles to Users
8. Find staff member
9. Assign "Office Staff" role
10. Done!

---

## Permission Naming Convention

For any module, follow this pattern:

```
view_{module}     - View/Read access
create_{module}   - Create/Add access
edit_{module}     - Edit/Update access
delete_{module}   - Delete/Remove access
manage_{module}   - Full access (all above)
```

**Examples:**
- `view_students`, `create_students`, `edit_students`, `delete_students`
- `view_teachers`, `create_teachers`, `edit_teachers`, `delete_teachers`
- `view_fees`, `collect_fees`, `manage_fee_structure`

---

## Multiple Roles

Ek user ko multiple roles de sakte ho:

**Example:**
```
Teacher "Suresh Kumar" ko:
- Teacher role (basic permissions)
- Class Teacher role (extra permissions)
- Sports Coordinator role (sports permissions)

Total Permissions = Teacher + Class Teacher + Sports Coordinator
```

---

## Check Current Permissions

### In Code:
```php
// Get all permissions of a user
$user = auth()->user();
$permissions = $user->getAllPermissions();

// Check specific permission
if ($user->hasPermission('edit_students')) {
    // Allow
}

// Check multiple permissions (any)
if ($user->hasAnyPermission(['edit_students', 'delete_students'])) {
    // Allow
}

// Check role
if ($user->hasRole('teacher')) {
    // Allow
}
```

### In Database:
```sql
-- Check user's roles
SELECT r.* FROM roles r
JOIN admin_roles ar ON r.id = ar.role_id
WHERE ar.admin_id = 1;

-- Check role's permissions
SELECT p.* FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
WHERE rp.role_id = 1;

-- Check user's all permissions (through roles)
SELECT DISTINCT p.* FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
JOIN admin_roles ar ON rp.role_id = ar.role_id
WHERE ar.admin_id = 1;
```

---

## Troubleshooting

### Issue: Permission nahi mil rahi
**Solution:**
1. Check user ko role assigned hai ya nahi
2. Check role ko permission assigned hai ya nahi
3. User logout/login karwao
4. Cache clear karo

### Issue: Edit button dikh raha hai but click nahi ho raha
**Solution:**
1. Controller mein permission check add karo
2. Middleware add karo route mein

### Issue: Permission check karne par error
**Solution:**
1. Check HasRolesAndPermissions trait model mein use ho raha hai
2. Check roles() relationship defined hai

---

## Quick Reference

### Frontend URLs:
- Roles: `/admin/settings/roles`
- Permissions: `/admin/settings/permissions`
- Assign Permissions: `/admin/settings/assign-permissions`
- Assign Roles: `/admin/settings/assign-roles`

### Code Checks:
```php
// Permission check
auth()->user()->hasPermission('edit_students')

// Role check
auth()->user()->hasRole('teacher')

// Multiple permissions (any)
auth()->user()->hasAnyPermission(['edit', 'delete'])

// Get all permissions
auth()->user()->getAllPermissions()
```

### Blade Checks:
```blade
@if(auth()->user()->hasPermission('edit_students'))
    <!-- Show edit button -->
@endif

@if(auth()->user()->hasRole('teacher'))
    <!-- Show teacher menu -->
@endif
```

---

## Summary

**To give edit, delete, view access:**

1. **Create/Check Permissions** (Settings → Permissions)
2. **Assign to Role** (Settings → Assign Permissions)
3. **Assign Role to User** (Settings → Assign Roles)
4. **Verify** (Check Current Roles column)
5. **Test** (Login as that user and check)

**Formula:**
```
Permission → Role → User = Access ✅
```

---

**Last Updated:** 2026-02-07
**Status:** Complete Guide
