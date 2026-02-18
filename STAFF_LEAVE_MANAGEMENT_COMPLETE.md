# 📋 Staff Leave Management - Complete Guide

## ✅ System Successfully Setup!

**Date**: February 11, 2026  
**Status**: Fully Functional

---

## 🗄️ Database Structure

### `staff_leaves` Table
```sql
- id (primary key)
- staff_id (foreign key → staff_members)
- leave_type (Sick Leave, Casual Leave, Earned Leave, Maternity Leave, Paternity Leave, Other)
- from_date (date)
- to_date (date)
- total_days (integer)
- reason (text)
- status (Pending, Approved, Rejected) - default: Pending
- admin_remarks (text, nullable)
- approved_by (foreign key → admins, nullable)
- deleted_at (soft delete)
- created_at, updated_at
```

### Indexes
- staff_id
- status
- from_date, to_date (composite)

---

## 📊 Features

### 1. Apply Leave
- Staff member select karo
- Leave type choose karo
- From date aur To date select karo
- Total days automatically calculate hote hain
- Reason enter karo
- Submit karo

### 2. View All Leaves
- Sab leave applications table mein dikhte hain
- Status ke sath: Pending, Approved, Rejected
- Staff name, leave type, dates, reason visible

### 3. Approve/Reject Leave
- Admin leave ko approve ya reject kar sakta hai
- Admin remarks add kar sakta hai
- Approved by admin ka naam save hota hai

### 4. Leave Types
1. **Sick Leave** - Bimari ke liye
2. **Casual Leave** - Personal kaam ke liye
3. **Earned Leave** - Earned vacation
4. **Maternity Leave** - Mahila staff ke liye
5. **Paternity Leave** - Male staff ke liye
6. **Other** - Koi aur reason

---

## 💻 How It Works

### Frontend (View)
**URL**: `/admin/staff/leave`

**Page Structure**:
1. **Apply Leave Form**
   - Staff dropdown
   - Leave type dropdown
   - Date range picker
   - Reason textarea
   - Submit button

2. **Leave Applications Table**
   - Employee ID
   - Staff Name
   - Leave Type
   - From Date → To Date
   - Total Days
   - Status (badge)
   - Actions (Approve/Reject buttons)

### Backend (Controller)

#### 1. Get All Leaves
```php
GET /admin/staff/leave/data
Response: { data: [...leaves with staff info...] }
```

#### 2. Apply Leave
```php
POST /admin/staff/leave/apply
Body: {
  staff_id, leave_type, from_date, to_date, reason
}
Response: { success: true, message: "Leave applied", data: {...} }
```

#### 3. Update Leave Status
```php
PUT /admin/staff/leave/{id}/status
Body: {
  status: "Approved" or "Rejected",
  admin_remarks: "..."
}
Response: { success: true, message: "Status updated", data: {...} }
```

---

## 🧪 Testing Steps

### Test 1: Apply Leave
1. Go to: Staff → Leave Management
2. Select Staff: pankaj maurya (or any staff)
3. Leave Type: Sick Leave
4. From Date: 2026-02-15
5. To Date: 2026-02-17
6. Reason: "Fever and cold"
7. Click "Apply Leave"
8. ✅ Success message aana chahiye
9. ✅ Table mein new entry dikhni chahiye with status "Pending"

### Test 2: Approve Leave
1. Find the leave application in table
2. Click "Approve" button
3. Add admin remarks (optional): "Approved for medical reasons"
4. Confirm
5. ✅ Status change to "Approved"
6. ✅ Badge color change (green)

### Test 3: Reject Leave
1. Apply another leave
2. Click "Reject" button
3. Add admin remarks: "Not enough staff available"
4. Confirm
5. ✅ Status change to "Rejected"
6. ✅ Badge color change (red)

### Test 4: Total Days Calculation
1. From Date: 2026-02-10
2. To Date: 2026-02-14
3. ✅ Total Days should be: 5
4. Calculation: (14 - 10) + 1 = 5 days

---

## 🎨 UI Components

### Status Badges
```html
Pending  → Yellow badge
Approved → Green badge
Rejected → Red badge
```

### Leave Type Colors
```
Sick Leave      → Red icon
Casual Leave    → Blue icon
Earned Leave    → Green icon
Maternity Leave → Pink icon
Paternity Leave → Purple icon
Other           → Gray icon
```

---

## 📁 Important Files

### Model
```
app/Models/StaffLeave.php
```

### Controller
```
app/Http/Controllers/StaffController.php
Methods:
- leave()
- getLeaveData()
- applyLeave()
- updateLeaveStatus()
```

### View
```
resources/views/admin/staff/leave.blade.php
```

### Migration
```
database/migrations/2026_02_07_152845_create_staff_leaves_table.php
```

### Routes
```php
Route::get('/staff/leave', [StaffController::class, 'leave'])
    ->name('admin.staff.leave');

Route::get('/staff/leave/data', [StaffController::class, 'getLeaveData'])
    ->name('admin.staff.leave.data');

Route::post('/staff/leave/apply', [StaffController::class, 'applyLeave'])
    ->name('admin.staff.leave.apply');

Route::put('/staff/leave/{id}/status', [StaffController::class, 'updateLeaveStatus'])
    ->name('admin.staff.leave.status');
```

---

## 🔧 Model Methods

### Relationships
```php
$leave->staff         // Get staff member
$leave->approvedBy    // Get admin who approved
```

### Scopes
```php
StaffLeave::pending()->get()   // Only pending leaves
StaffLeave::approved()->get()  // Only approved leaves
StaffLeave::rejected()->get()  // Only rejected leaves
```

### Usage Example
```php
// Get all pending leaves for a staff
$pendingLeaves = StaffLeave::where('staff_id', $staffId)
    ->pending()
    ->get();

// Get approved leaves in date range
$leaves = StaffLeave::approved()
    ->whereBetween('from_date', [$startDate, $endDate])
    ->with('staff')
    ->get();
```

---

## 🐛 Common Issues & Solutions

### Issue 1: Staff Dropdown Empty
**Solution**: 
- Check staff_members table mein data hai
- Add staff: Staff → Add Staff

### Issue 2: Total Days Wrong
**Solution**:
- Formula: (to_date - from_date) + 1
- Example: Feb 10 to Feb 14 = 5 days (not 4)

### Issue 3: Leave Not Saving
**Solution**:
- Check all required fields filled hain
- Check dates: to_date >= from_date
- Check browser console for errors

### Issue 4: Status Not Updating
**Solution**:
- Check admin is logged in
- Check CSRF token
- Check route is correct

---

## 📊 Reports (Future Enhancement)

### Leave Summary Report
- Total leaves by staff
- Leave type breakdown
- Approval rate
- Most common leave reasons

### Monthly Leave Calendar
- Visual calendar showing who is on leave
- Color-coded by leave type
- Export to PDF

### Leave Balance
- Track remaining leave days per staff
- Sick leave quota
- Casual leave quota
- Earned leave accumulation

---

## ✅ Summary

**Staff Leave Management ab fully functional hai!**

### What's Working:
- ✅ Complete database structure
- ✅ StaffLeave model with relationships
- ✅ Controller methods for CRUD operations
- ✅ Frontend view with form and table
- ✅ Apply leave functionality
- ✅ Approve/Reject functionality
- ✅ Status tracking
- ✅ Admin remarks
- ✅ Soft deletes enabled

### How to Use:
1. **Staff → Leave Management** pe jao
2. Leave apply karo
3. Admin approve/reject kare
4. Status track karo

**Happy Managing! 🎉**
