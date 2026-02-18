# Simple Example - Teacher Ko Edit/Delete Access Dena

## Scenario
Teacher "Ramesh Kumar" ko students edit aur delete karne ka access dena hai.

---

## Step 1: Check Permissions Exist Karte Hain Ya Nahi

1. Browser mein jao: `http://localhost:8000/admin/settings/permissions`
2. Students module dekho
3. Check karo ye permissions hain:
   - `edit_students` - Edit Students
   - `delete_students` - Delete Students
   - `view_students` - View Students

**Agar nahi hain toh:**
1. Click "Add New Permission"
2. Create karo:
   - Name: `edit_students`
   - Display Name: `Edit Students`
   - Module: `students`
3. Save karo
4. Same for `delete_students`

---

## Step 2: Teacher Role Ko Permissions Do

1. Jao: `http://localhost:8000/admin/settings/assign-permissions`
2. Left side se **"Teacher"** role par click karo
3. Right side mein Students module dhundo
4. Check karo:
   - ☑ View Students
   - ☑ Edit Students
   - ☑ Delete Students
5. Niche **"Save Permissions"** button click karo
6. Green success popup dikhega: "Permissions assigned successfully!"

---

## Step 3: Ramesh Kumar Ko Teacher Role Do

1. Jao: `http://localhost:8000/admin/settings/assign-roles`
2. **Teachers** section mein scroll karo
3. "Ramesh Kumar" dhundo
4. Uske saamne **"Assign Roles"** button click karo
5. Modal open hoga
6. **"Teacher"** role check karo
7. **"Save Roles"** button click karo
8. Green success popup: "Roles assigned successfully!"
9. "Current Roles" column mein "Teacher" badge dikhega

---

## Step 4: Verify Karo

### Database Check:
```sql
-- Check Ramesh Kumar ki ID
SELECT id, first_name, last_name FROM teachers WHERE first_name = 'Ramesh';

-- Check uske roles (assume id = 1)
SELECT r.name, r.display_name 
FROM roles r
JOIN teacher_roles tr ON r.id = tr.role_id
WHERE tr.teacher_id = 1;

-- Check role ke permissions
SELECT p.name, p.display_name
FROM permissions p
JOIN role_permissions rp ON p.id = rp.permission_id
JOIN teacher_roles tr ON rp.role_id = tr.role_id
WHERE tr.teacher_id = 1;
```

### Code Check:
```php
$teacher = Teacher::find(1); // Ramesh Kumar
$teacher->hasPermission('edit_students');   // Should return true
$teacher->hasPermission('delete_students'); // Should return true
$teacher->hasRole('teacher');               // Should return true
```

---

## Step 5: Use in Code

### In Controller:
```php
public function edit($id)
{
    // Check permission
    if (!auth()->guard('teacher')->user()->hasPermission('edit_students')) {
        return redirect()->back()->with('error', 'You do not have permission to edit students');
    }
    
    $student = Student::findOrFail($id);
    return view('students.edit', compact('student'));
}

public function destroy($id)
{
    // Check permission
    if (!auth()->guard('teacher')->user()->hasPermission('delete_students')) {
        return redirect()->back()->with('error', 'You do not have permission to delete students');
    }
    
    Student::findOrFail($id)->delete();
    return redirect()->back()->with('success', 'Student deleted successfully');
}
```

### In Blade View:
```blade
{{-- Show edit button only if teacher has permission --}}
@if(auth()->guard('teacher')->user()->hasPermission('edit_students'))
    <a href="{{ route('students.edit', $student->id) }}" class="btn btn-warning btn-sm">
        <i class="fas fa-edit"></i> Edit
    </a>
@endif

{{-- Show delete button only if teacher has permission --}}
@if(auth()->guard('teacher')->user()->hasPermission('delete_students'))
    <button onclick="deleteStudent({{ $student->id }})" class="btn btn-danger btn-sm">
        <i class="fas fa-trash"></i> Delete
    </button>
@endif
```

---

## Done! ✅

Ab Ramesh Kumar (Teacher) ko:
- ✅ Students view karne ka access hai
- ✅ Students edit karne ka access hai
- ✅ Students delete karne ka access hai

---

## Quick Summary

```
1. Permissions check/create karo (Settings → Permissions)
2. Role ko permissions do (Settings → Assign Permissions)
3. User ko role do (Settings → Assign Roles)
4. Code mein use karo (hasPermission check)
5. Done! ✅
```

---

## Visual Flow

```
Permission: edit_students
    ↓
Role: Teacher
    ↓
User: Ramesh Kumar
    ↓
Access: Can edit students ✅
```

---

**Time Required:** 2-3 minutes
**Difficulty:** Easy
**Result:** Teacher can now edit and delete students!
