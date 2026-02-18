# Test Permissions System - Checklist

## Pre-requisites
- [ ] Database migrated
- [ ] Seeder run kiya (RolesAndPermissionsSeeder)
- [ ] Admin login hai

## Test 1: Roles Management

### Add New Role
1. [ ] Navigate to: Settings → Roles Management
2. [ ] Click "Add New Role" button
3. [ ] Fill form:
   - Name: `test_role`
   - Display Name: `Test Role`
   - Description: `This is a test role`
   - Active: Checked
4. [ ] Click "Save Role"
5. [ ] Success message dikhna chahiye
6. [ ] Table mein new role dikhna chahiye

### Edit Role
1. [ ] Find "Test Role" in table
2. [ ] Click edit (pencil) icon
3. [ ] Change Display Name to: `Test Role Updated`
4. [ ] Click "Update Role"
5. [ ] Success message dikhna chahiye
6. [ ] Updated name table mein dikhna chahiye

### Delete Role
1. [ ] Find "Test Role" in table
2. [ ] Click delete (trash) icon
3. [ ] Confirm deletion
4. [ ] Success message dikhna chahiye
5. [ ] Role table se remove ho jana chahiye

**Expected Results:**
- ✅ Add works
- ✅ Edit works
- ✅ Delete works
- ✅ Success messages show
- ✅ Table updates automatically

---

## Test 2: Permissions Management

### Add New Permission
1. [ ] Navigate to: Settings → Permissions Management
2. [ ] Click "Add New Permission" button
3. [ ] Fill form:
   - Name: `test_permission`
   - Display Name: `Test Permission`
   - Module: `students`
   - Description: `Test permission`
   - Active: Checked
4. [ ] Click "Save Permission"
5. [ ] Success message dikhna chahiye
6. [ ] Students module mein new permission dikhna chahiye

### Edit Permission
1. [ ] Find "Test Permission" in Students module
2. [ ] Click edit icon
3. [ ] Change Display Name to: `Test Permission Updated`
4. [ ] Click "Update Permission"
5. [ ] Success message dikhna chahiye
6. [ ] Updated name dikhna chahiye

### Delete Permission
1. [ ] Find "Test Permission"
2. [ ] Click delete icon
3. [ ] Confirm deletion
4. [ ] Success message dikhna chahiye
5. [ ] Permission remove ho jana chahiye

**Expected Results:**
- ✅ Add works
- ✅ Edit works
- ✅ Delete works
- ✅ Module-wise grouping works
- ✅ Success messages show

---

## Test 3: Assign Permissions to Role

### Select Role
1. [ ] Navigate to: Settings → Assign Permissions
2. [ ] Left panel mein roles list dikhni chahiye
3. [ ] Click on "Teacher" role
4. [ ] Right panel mein permissions dikhne chahiye
5. [ ] Current assigned permissions checked hone chahiye

### Assign Permissions
1. [ ] "Teacher" role selected hai
2. [ ] Students module mein:
   - [ ] Check "View Students"
   - [ ] Check "Create Students"
3. [ ] Attendance module mein:
   - [ ] Check "View Attendance"
   - [ ] Check "Mark Attendance"
4. [ ] Click "Save Permissions"
5. [ ] Success message dikhna chahiye
6. [ ] Page reload karne par permissions checked rahne chahiye

### Toggle Module
1. [ ] Click "Toggle All" button for any module
2. [ ] All permissions in that module toggle hone chahiye
3. [ ] Again click to toggle back

### Select/Deselect All
1. [ ] Click "Select All Permissions" button
2. [ ] All checkboxes checked hone chahiye
3. [ ] Click "Deselect All" button
4. [ ] All checkboxes unchecked hone chahiye

**Expected Results:**
- ✅ Role selection works
- ✅ Permissions display correctly
- ✅ Save works
- ✅ Toggle buttons work
- ✅ Current permissions persist

---

## Test 4: Assign Roles to Users

### Assign Role to Admin
1. [ ] Navigate to: Settings → Assign Roles to Users
2. [ ] Admins section mein admin list dikhni chahiye
3. [ ] Find any admin
4. [ ] Click "Assign Roles" button
5. [ ] Modal open hona chahiye
6. [ ] Check "Admin" role
7. [ ] Click "Save Roles"
8. [ ] Success message dikhna chahiye
9. [ ] "Current Roles" column mein badge dikhna chahiye

### Assign Role to Teacher
1. [ ] Teachers section mein teacher list dikhni chahiye
2. [ ] Find any teacher
3. [ ] Click "Assign Roles" button
4. [ ] Check "Teacher" role
5. [ ] Click "Save Roles"
6. [ ] Success message dikhna chahiye
7. [ ] Badge dikhna chahiye

### Assign Multiple Roles
1. [ ] Find any user
2. [ ] Click "Assign Roles"
3. [ ] Check multiple roles:
   - [ ] Teacher
   - [ ] Class Teacher (if exists)
4. [ ] Save
5. [ ] Multiple badges dikhne chahiye

### Remove Role
1. [ ] Find user with assigned role
2. [ ] Click "Assign Roles"
3. [ ] Uncheck the role
4. [ ] Save
5. [ ] Badge remove ho jana chahiye

**Expected Results:**
- ✅ All user types show (Admin, Teacher, Staff)
- ✅ Assign works
- ✅ Multiple roles work
- ✅ Remove works
- ✅ Badges display correctly

---

## Test 5: Validation & Error Handling

### Duplicate Role Name
1. [ ] Try to create role with existing name
2. [ ] Error message dikhna chahiye
3. [ ] Form submit nahi hona chahiye

### Duplicate Permission Name
1. [ ] Try to create permission with existing name
2. [ ] Error message dikhna chahiye

### Delete Role with Users
1. [ ] Assign a role to a user
2. [ ] Try to delete that role
3. [ ] Error message: "Cannot delete role. It is assigned to X user(s)"
4. [ ] Role delete nahi hona chahiye

### Delete Permission with Roles
1. [ ] Assign a permission to a role
2. [ ] Try to delete that permission
3. [ ] Error message: "Cannot delete permission. It is assigned to X role(s)"
4. [ ] Permission delete nahi hona chahiye

**Expected Results:**
- ✅ Validation works
- ✅ Error messages show
- ✅ Duplicate prevention works
- ✅ Cascade delete prevention works

---

## Test 6: UI/UX

### Responsive Design
1. [ ] Open on mobile/tablet
2. [ ] All elements visible
3. [ ] Buttons clickable
4. [ ] Modals work properly

### Loading States
1. [ ] Click save button
2. [ ] Button should show "Saving..." with spinner
3. [ ] Button should be disabled during save

### Success Messages
1. [ ] All success messages auto-dismiss or have close button
2. [ ] Messages are clearly visible
3. [ ] Messages are descriptive

### Active States
1. [ ] Selected role in assign permissions should be highlighted
2. [ ] Active menu item should be highlighted
3. [ ] Hover effects work

**Expected Results:**
- ✅ Mobile responsive
- ✅ Loading states work
- ✅ Messages clear
- ✅ Visual feedback good

---

## Test 7: Database Verification

### Check Tables
```sql
-- Check roles
SELECT * FROM roles;

-- Check permissions
SELECT * FROM permissions;

-- Check role_permissions
SELECT * FROM role_permissions;

-- Check admin_roles
SELECT * FROM admin_roles;

-- Check teacher_roles
SELECT * FROM teacher_roles;
```

**Expected Results:**
- ✅ All tables exist
- ✅ Data is being saved
- ✅ Relationships work
- ✅ No orphaned records

---

## Test 8: Permission Check in Code

### Test hasPermission()
```php
$admin = Admin::find(1);
$admin->assignRole('admin');

// Should return true if admin role has this permission
$admin->hasPermission('create_students');
```

### Test hasRole()
```php
$admin = Admin::find(1);
$admin->assignRole('admin');

// Should return true
$admin->hasRole('admin');
```

**Expected Results:**
- ✅ hasPermission() works
- ✅ hasRole() works
- ✅ Trait methods work

---

## Common Issues & Solutions

### Issue: Pages not loading
**Solution:** 
- Check routes are added
- Clear cache: `php artisan route:clear`

### Issue: Permissions not saving
**Solution:**
- Check CSRF token
- Check form action URL
- Check controller method

### Issue: Roles not showing
**Solution:**
- Run seeder: `php artisan db:seed --class=RolesAndPermissionsSeeder`
- Check database connection

### Issue: Delete not working
**Solution:**
- Check JavaScript console for errors
- Verify CSRF token
- Check route method (DELETE)

---

## Final Checklist

- [ ] All CRUD operations work for Roles
- [ ] All CRUD operations work for Permissions
- [ ] Assign Permissions to Roles works
- [ ] Assign Roles to Users works
- [ ] Validation works
- [ ] Error handling works
- [ ] UI is responsive
- [ ] Success messages show
- [ ] Database updates correctly
- [ ] No console errors
- [ ] No PHP errors

---

## Test Summary

**Total Tests:** 8 categories
**Total Checkpoints:** 100+

**Status:**
- [ ] All tests passed ✅
- [ ] Some tests failed ❌ (list below)
- [ ] Not tested yet ⏳

**Failed Tests:**
1. _____________________
2. _____________________
3. _____________________

**Notes:**
_____________________
_____________________
_____________________

---

**Testing Date:** __________
**Tested By:** __________
**Environment:** Development / Production
