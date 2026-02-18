# Roles & Permissions Frontend Management Guide

## Overview
Ab aap frontend se complete roles aur permissions manage kar sakte ho!

## Access URLs

### 1. Roles Management
**URL:** `/admin/settings/roles`

**Features:**
- View all roles
- Add new role
- Edit existing role
- Delete role
- See permission count for each role

**Fields:**
- Name: Unique identifier (lowercase, underscore)
- Display Name: User-friendly name
- Description: Role description
- Status: Active/Inactive

---

### 2. Permissions Management
**URL:** `/admin/settings/permissions`

**Features:**
- View all permissions (grouped by module)
- Add new permission
- Edit existing permission
- Delete permission
- Module-wise organization

**Fields:**
- Name: Unique identifier (lowercase, underscore)
- Display Name: User-friendly name
- Module: Category (students, teachers, fees, etc.)
- Description: Permission description
- Status: Active/Inactive

**Modules:**
- students
- teachers
- staff
- attendance
- exams
- fees
- library
- academic
- reports
- settings

---

### 3. Assign Permissions to Roles
**URL:** `/admin/settings/assign-permissions`

**Features:**
- Select a role from left panel
- View all available permissions (grouped by module)
- Check/uncheck permissions
- Select all in a module
- Select/deselect all permissions
- Save assignments

**How to use:**
1. Click on a role from left panel
2. Right side will show all permissions
3. Check the permissions you want to assign
4. Click "Save Permissions"

---

### 4. Assign Roles to Users
**URL:** `/admin/settings/assign-roles`

**Features:**
- Assign roles to Admins
- Assign roles to Teachers
- Assign roles to Staff Members
- View current roles for each user
- Multiple roles can be assigned to one user

**How to use:**
1. Find the user in respective section (Admin/Teacher/Staff)
2. Click "Assign Roles" button
3. Select roles from modal
4. Click "Save Roles"

---

## Navigation

Settings menu mein ye links add karein:

```html
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.settings.roles') }}">
        <i class="fas fa-user-tag"></i> Roles
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.settings.permissions') }}">
        <i class="fas fa-key"></i> Permissions
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.settings.assign.permissions') }}">
        <i class="fas fa-link"></i> Assign Permissions
    </a>
</li>
<li class="nav-item">
    <a class="nav-link" href="{{ route('admin.settings.assign.roles') }}">
        <i class="fas fa-users-cog"></i> Assign Roles
    </a>
</li>
```

---

## Usage Examples

### Example 1: Create New Role
1. Go to `/admin/settings/roles`
2. Click "Add New Role"
3. Fill form:
   - Name: `principal`
   - Display Name: `Principal`
   - Description: `School Principal with full access`
4. Click "Save Role"

### Example 2: Assign Permissions to Role
1. Go to `/admin/settings/assign-permissions`
2. Click on "Principal" role from left
3. Select all permissions or specific ones
4. Click "Save Permissions"

### Example 3: Assign Role to Admin
1. Go to `/admin/settings/assign-roles`
2. Find admin in Admins section
3. Click "Assign Roles"
4. Check "Principal" role
5. Click "Save Roles"

---

## API Endpoints

### Roles
- `GET /admin/settings/roles` - View roles page
- `POST /admin/settings/roles` - Create new role
- `PUT /admin/settings/roles/{id}` - Update role
- `DELETE /admin/settings/roles/{id}` - Delete role

### Permissions
- `GET /admin/settings/permissions` - View permissions page
- `POST /admin/settings/permissions` - Create new permission
- `PUT /admin/settings/permissions/{id}` - Update permission
- `DELETE /admin/settings/permissions/{id}` - Delete permission

### Assign Permissions
- `GET /admin/settings/assign-permissions` - View assign page
- `POST /admin/settings/roles/{id}/permissions` - Update role permissions

### Assign Roles
- `GET /admin/settings/assign-roles` - View assign page
- `POST /admin/settings/admins/{id}/roles` - Assign roles to admin
- `POST /admin/settings/teachers/{id}/roles` - Assign roles to teacher
- `POST /admin/settings/staff/{id}/roles` - Assign roles to staff

---

## Security Notes

1. **Super Admin Protection**: Super admin role ko delete mat karna
2. **Permission Check**: Important actions ke liye permission check lagana
3. **Validation**: Unique names ensure karna
4. **Audit Trail**: Future mein role changes ka log rakhna

---

## Default Data

System mein already ye data seeded hai:

**Roles:**
- Super Admin (All permissions)
- Admin (Most permissions)
- Teacher (Limited permissions)
- Accountant (Fee management)
- Librarian (Library management)

**Permissions:** 32 permissions across 9 modules

---

## Troubleshooting

### Issue: Roles not showing
**Solution:** Run seeder
```bash
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### Issue: Permission denied
**Solution:** Check if user has required role/permission

### Issue: Changes not reflecting
**Solution:** Clear cache
```bash
php artisan cache:clear
php artisan config:clear
```

---

## Future Enhancements

1. Permission groups
2. Role hierarchy
3. Audit logs
4. Bulk operations
5. Import/Export roles
6. Permission templates
