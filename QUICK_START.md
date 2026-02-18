# 🚀 Quick Start Guide - School ERP System

## Prerequisites Check ✅

Before starting, make sure you have:
- ✅ PHP 8.2 or higher
- ✅ Composer installed
- ✅ PostgreSQL 12+ installed and running
- ✅ PostgreSQL database created (name: `school_erp`)

## Step-by-Step Installation

### 1️⃣ Database Setup

Open PostgreSQL and create database:
```sql
CREATE DATABASE school_erp;
```

### 2️⃣ Configure Environment

The `.env` file is already configured with:
```
DB_CONNECTION=pgsql
DB_HOST=localhost
DB_PORT=5432
DB_DATABASE=school_erp
DB_USERNAME=postgres
DB_PASSWORD=pankaj123@
```

**⚠️ Important:** Update `DB_PASSWORD` if your PostgreSQL password is different!

### 3️⃣ Run Complete Setup

Simply double-click on:
```
COMPLETE_SETUP.bat
```

This will automatically:
- Install all dependencies
- Generate application key
- Run database migrations
- Create storage links
- Seed sample data
- Clear all caches

### 4️⃣ Start the Server

After setup completes, run:
```bash
php artisan serve
```

### 5️⃣ Access the System

Open your browser and visit:
```
http://localhost:8000
```

## 🔐 Default Login Credentials

### Student Login
- **Username:** `student001`
- **Password:** `password`
- **User Type:** Student

### Teacher Login
- **Username:** `teacher001`
- **Password:** `password`
- **User Type:** Teacher

### Admin Login
- **Username:** `admin`
- **Password:** `admin123`
- **User Type:** Admin

## 📋 What's Included?

### ✅ Complete Features
- Multi-user authentication (Student, Teacher, Admin)
- Student admission and management
- Teacher/Staff management
- Master data management (Sessions, Classes, Sections, Subjects)
- Beautiful dashboards for each user type
- AJAX-based operations (no page reload)
- SweetAlert2 notifications
- Responsive Bootstrap 5 UI

### 📁 Database Tables
- `sessions` - Academic year management
- `classes` - Class information
- `sections` - Section details
- `subjects` - Subject master
- `students` - Student records
- `teachers` - Teacher/staff records
- `class_subjects` - Class-subject mapping
- `teacher_subjects` - Teacher-subject assignment

## 🎯 Quick Actions

### Add New Student
1. Login as Admin
2. Go to Students → New Admission
3. Fill the form
4. Submit

### Add New Teacher
1. Login as Admin
2. Go to Teachers → Add Teacher
3. Fill the form
4. Submit

### Manage Master Data
1. Login as Admin
2. Go to Master → Sessions/Classes/Sections/Subjects
3. Add/Edit/Delete records

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check if PostgreSQL is running
# Windows: Check Services → PostgreSQL

# Test connection
psql -U postgres -d school_erp
```

### Permission Issues
```bash
# Give write permissions to storage
icacls storage /grant Everyone:F /T
icacls bootstrap\cache /grant Everyone:F /T
```

### Clear All Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### Regenerate Key
```bash
php artisan key:generate
```

### Reset Database
```bash
php artisan migrate:fresh --seed
```

## 📞 Need Help?

Check these files for more information:
- `README.md` - Complete documentation
- `HINDI_GUIDE.md` - Hindi guide
- `PROJECT_SUMMARY.md` - Project overview
- `INSTALLATION_COMPLETE.md` - Detailed installation

## 🎨 Technology Stack

- **Backend:** Laravel 11
- **Database:** PostgreSQL
- **Frontend:** Bootstrap 5, jQuery, AJAX
- **Notifications:** SweetAlert2
- **Icons:** Font Awesome 6

---

**Happy Coding! 🎓**
