# Permission Assignment Flow Diagram

## Simple Flow

```
┌─────────────┐
│ Permission  │  (e.g., "create_students")
└──────┬──────┘
       │
       │ Assign to
       ▼
┌─────────────┐
│    Role     │  (e.g., "Teacher")
└──────┬──────┘
       │
       │ Assign to
       ▼
┌─────────────┐
│    User     │  (e.g., "Ramesh Kumar")
└─────────────┘
```

---

## Detailed Process Flow

```
START
  │
  ├─── Step 1: Create/Select Role
  │    │
  │    ├─ Option A: Use Existing Role
  │    │   └─ (Teacher, Admin, Accountant, etc.)
  │    │
  │    └─ Option B: Create New Role
  │        └─ Settings → Roles Management → Add New Role
  │
  ├─── Step 2: Assign Permissions to Role
  │    │
  │    └─ Settings → Assign Permissions
  │        │
  │        ├─ Select Role from left panel
  │        ├─ Check permissions from right panel
  │        └─ Click "Save Permissions"
  │
  ├─── Step 3: Assign Role to User
  │    │
  │    └─ Settings → Assign Roles to Users
  │        │
  │        ├─ Find user (Admin/Teacher/Staff)
  │        ├─ Click "Assign Roles" button
  │        ├─ Select roles in modal
  │        └─ Click "Save Roles"
  │
  └─── DONE ✅
       User now has permissions!
```

---

## Example: Teacher Ko Attendance Permission Dena

```
┌──────────────────────────────────────────────────┐
│ GOAL: Teacher ko attendance mark karne ki        │
│       permission deni hai                        │
└──────────────────────────────────────────────────┘
                    │
                    ▼
┌──────────────────────────────────────────────────┐
│ Step 1: Role Select Karo                         │
│ ✓ "Teacher" role already exist karta hai         │
└──────────────────────────────────────────────────┘
                    │
                    ▼
┌──────────────────────────────────────────────────┐
│ Step 2: Permissions Assign Karo                  │
│                                                   │
│ Settings → Assign Permissions                    │
│   │                                               │
│   ├─ Select "Teacher" role                       │
│   │                                               │
│   └─ Attendance Module:                          │
│       ☑ view_attendance                          │
│       ☑ mark_attendance                          │
│                                                   │
│   Click "Save Permissions"                       │
└──────────────────────────────────────────────────┘
                    │
                    ▼
┌──────────────────────────────────────────────────┐
│ Step 3: User Ko Role Assign Karo                 │
│                                                   │
│ Settings → Assign Roles to Users                 │
│   │                                               │
│   ├─ Teachers section mein jao                   │
│   ├─ "Ramesh Kumar" dhundo                       │
│   ├─ "Assign Roles" button click karo            │
│   │                                               │
│   └─ Modal mein:                                 │
│       ☑ Teacher                                  │
│                                                   │
│   Click "Save Roles"                             │
└──────────────────────────────────────────────────┘
                    │
                    ▼
┌──────────────────────────────────────────────────┐
│ RESULT ✅                                         │
│                                                   │
│ Ramesh Kumar (Teacher) ab:                       │
│ • Attendance dekh sakta hai                      │
│ • Attendance mark kar sakta hai                  │
└──────────────────────────────────────────────────┘
```

---

## Multiple Roles Example

```
User: "Suresh Sharma"
  │
  ├─ Role 1: Teacher
  │   └─ Permissions:
  │       • view_students
  │       • mark_attendance
  │       • enter_marks
  │
  └─ Role 2: Class Teacher
      └─ Permissions:
          • manage_class
          • view_reports
          • contact_parents

TOTAL PERMISSIONS = Role 1 + Role 2
                  = All combined permissions
```

---

## Permission Check Flow (In Code)

```
User Login
    │
    ▼
User tries to access a feature
    │
    ▼
System checks: Does user have permission?
    │
    ├─ YES → Allow access ✅
    │
    └─ NO → Show error "Permission Denied" ❌
```

---

## Database Structure

```
┌─────────────┐
│ permissions │
│ (32 items)  │
└──────┬──────┘
       │
       │ Many-to-Many
       ▼
┌─────────────────┐
│ role_permissions│ (Pivot Table)
└──────┬──────────┘
       │
       │ Many-to-Many
       ▼
┌─────────────┐
│    roles    │
│  (5 items)  │
└──────┬──────┘
       │
       │ Many-to-Many
       ▼
┌─────────────────┐
│  admin_roles    │ (Pivot Table)
│  teacher_roles  │
│  staff_roles    │
│  user_roles     │
└──────┬──────────┘
       │
       │ Many-to-Many
       ▼
┌─────────────┐
│    users    │
│  (admins,   │
│  teachers,  │
│  staff)     │
└─────────────┘
```

---

## Quick Decision Tree

```
Kya karna hai?
    │
    ├─ Naya role banana hai?
    │   └─ Settings → Roles Management → Add New Role
    │
    ├─ Role ko permissions deni hain?
    │   └─ Settings → Assign Permissions
    │
    ├─ User ko role dena hai?
    │   └─ Settings → Assign Roles to Users
    │
    ├─ Permission check karna hai?
    │   └─ Settings → Assign Roles to Users → Current Roles column
    │
    └─ Permission remove karna hai?
        ├─ Option 1: Role se permission uncheck karo
        └─ Option 2: User se role remove karo
```

---

## Common Scenarios

### Scenario 1: New Teacher Join Kiya
```
1. Settings → Assign Roles to Users
2. Teachers section mein new teacher dhundo
3. Assign Roles → "Teacher" role select karo
4. Save → Done!
```

### Scenario 2: Teacher Ko Extra Permission Deni Hai
```
Option A: Existing role mein permission add karo
  └─ Settings → Assign Permissions → Teacher role → Add permission

Option B: New role banake assign karo
  └─ Settings → Roles Management → Create "Senior Teacher"
  └─ Settings → Assign Permissions → Assign permissions
  └─ Settings → Assign Roles → Teacher ko dono roles do
```

### Scenario 3: Permission Revoke Karni Hai
```
Option A: Role se permission remove karo
  └─ Settings → Assign Permissions → Uncheck permission

Option B: User se role remove karo
  └─ Settings → Assign Roles → Uncheck role
```

---

## Visual Menu Navigation

```
Admin Dashboard
    │
    └─ Top Menu Bar
        │
        └─ Settings ⚙️
            │
            ├─ General Settings
            ├─ School Info
            ├─ User Management
            ├─ ─────────────────
            ├─ 🏷️ Roles Management
            ├─ 🔑 Permissions Management
            ├─ 🔗 Assign Permissions      ← Permission assign karne ke liye
            └─ 👥 Assign Roles to Users   ← User ko role dene ke liye
```

---

## Summary Checklist

Permission dene ke liye ye steps follow karo:

- [ ] Step 1: Role select/create karo
- [ ] Step 2: Role ko permissions assign karo (Settings → Assign Permissions)
- [ ] Step 3: User ko role assign karo (Settings → Assign Roles to Users)
- [ ] Step 4: Verify karo (Current Roles column check karo)
- [ ] Step 5: Test karo (User login karke check karo)

---

**Ye flow follow karo aur kisi ko bhi permission de sakte ho! 🎯**
