# Menu Navigation Guide - Roles & Permissions

## Location in Menu

Roles aur Permissions management **Settings Menu** ke under hai.

## Menu Structure

```
📊 Dashboard
👨‍🎓 Students
   ├── All Students
   ├── Student Admission
   ├── All Admissions
   ├── Add Student
   ├── Student Strength
   ├── Student Promotion
   └── Student Details

👥 Staff
   ├── All Staff
   ├── Add Staff
   ├── Staff Attendance
   └── Leave Management

📅 Attendance
   ├── Student Attendance
   ├── Staff Attendance
   └── Attendance Report

📚 Academic
   ├── Classes
   ├── Sections
   ├── Subjects
   ├── Sessions
   └── Time Table

📝 Examination
   ├── Exam List
   ├── Exam Schedule
   ├── Enter Marks
   └── Results

📖 Library

💰 Fees
   ├── Collect Fees
   ├── Fee Structure
   └── Fee Report

⚙️ Settings
   ├── General Settings
   ├── School Info
   ├── User Management
   ├── ─────────────────
   ├── 🏷️ Roles Management          ← NEW
   ├── 🔑 Permissions Management    ← NEW
   ├── 🔗 Assign Permissions        ← NEW
   └── 👥 Assign Roles to Users     ← NEW
```

## Access Path

### Method 1: Direct URL
```
http://localhost:8000/admin/settings/roles
http://localhost:8000/admin/settings/permissions
http://localhost:8000/admin/settings/assign-permissions
http://localhost:8000/admin/settings/assign-roles
```

### Method 2: Through Menu
1. Login as Admin
2. Click on **Settings** menu (top navigation bar)
3. Dropdown menu khulega
4. Niche 4 new options dikhenge:
   - **Roles Management** - Roles create/edit/delete
   - **Permissions Management** - Permissions create/edit/delete
   - **Assign Permissions** - Role ko permissions assign karna
   - **Assign Roles to Users** - Users ko roles assign karna

## Visual Indicators

### Icons Used:
- 🏷️ **Roles Management**: `fa-user-tag`
- 🔑 **Permissions Management**: `fa-key`
- 🔗 **Assign Permissions**: `fa-link`
- 👥 **Assign Roles to Users**: `fa-users-cog`

### Active State:
Jab aap Settings ke kisi bhi page par honge, Settings menu **active** (highlighted) rahega.

## Quick Access Workflow

### Scenario 1: New Role Create karna
```
Settings → Roles Management → Add New Role Button
```

### Scenario 2: Permission Assign karna
```
Settings → Assign Permissions → Select Role → Check Permissions → Save
```

### Scenario 3: User ko Role dena
```
Settings → Assign Roles to Users → Find User → Assign Roles Button → Select Roles → Save
```

## Mobile View

Mobile devices par:
1. Top-right corner mein hamburger menu (☰) dikhega
2. Click karne par full menu slide-in hoga
3. Settings expand karke same options milenge

## Permissions Required

In pages ko access karne ke liye user ko ye permissions chahiye:
- `manage_settings` - Settings menu access
- `manage_users` - User management access

**Note:** Super Admin ko by default sab access hai.

## Troubleshooting

### Menu item nahi dikh raha?
1. Browser cache clear karein (Ctrl + Shift + R)
2. Check karein ki aap admin login ho
3. Check karein ki horizontal.blade.php file updated hai

### Permission denied error?
1. Check karein ki logged-in user ko required permissions hain
2. Super admin se login karke try karein

### Dropdown nahi khul raha?
1. JavaScript console check karein (F12)
2. Bootstrap JS properly load ho raha hai check karein
3. admin-script.js file check karein

## Color Coding (Future Enhancement)

Settings submenu items ko color code kar sakte hain:
- **Blue**: General settings
- **Green**: User management
- **Orange**: Roles & Permissions (Security related)

## Next Steps

1. Login karein as admin
2. Settings menu click karein
3. Naye 4 options dikhenge
4. Kisi bhi option par click karke explore karein

---

**Happy Managing! 🎉**
