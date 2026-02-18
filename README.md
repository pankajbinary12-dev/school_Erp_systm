# 🎓 School ERP System - Complete Professional Solution

[![Status](https://img.shields.io/badge/Status-Production%20Ready-success)]()
[![Laravel](https://img.shields.io/badge/Laravel-11-red)]()
[![PHP](https://img.shields.io/badge/PHP-8.2+-blue)]()
[![PostgreSQL](https://img.shields.io/badge/PostgreSQL-12+-blue)]()

## 🚀 Quick Start (3 Simple Steps!)

1. **Create Database:**
   ```sql
   CREATE DATABASE school_erp;
   ```

2. **Run Setup:**
   ```bash
   SETUP.bat
   ```

3. **Start Server:**
   ```bash
   START_SERVER.bat
   ```

**That's it!** Visit `http://localhost:8000` 🎉

---

## 🎓 Features

### ✅ Complete Functionality
- **Multi-User Login System** (Student & Teacher)
- **Student Management** (Admission, Update, Delete, Promote)
- **Teacher/Staff Management** (Add, Update, Delete)
- **Master Data Management**
  - Sessions (Academic Years)
  - Classes
  - Sections
  - Subjects
- **Student Dashboard** with personal information
- **Teacher Dashboard** with schedule and notifications
- **Professional UI** with Bootstrap 5
- **AJAX-based** operations (No page reload)
- **SweetAlert2** for beautiful notifications
- **PostgreSQL Database** support

## 🚀 Installation Methods

### Method 1: Automatic Setup (Recommended) ⚡

**Just 3 commands:**

```bash
# 1. Create database
psql -U postgres -c "CREATE DATABASE school_erp;"

# 2. Run setup
SETUP.bat

# 3. Start server
START_SERVER.bat
```

### Method 2: Manual Setup 🔧

```bash
# Step 1: Install dependencies
composer install

# Step 2: Generate application key
php artisan key:generate

# Step 3: Configure database in .env
# DB_DATABASE=school_erp
# DB_USERNAME=postgres
# DB_PASSWORD=your_password

# Step 4: Run migrations
php artisan migrate

# Step 5: Seed sample data
php artisan db:seed

# Step 6: Create storage link
php artisan storage:link

# Step 7: Clear cache
php artisan optimize:clear

# Step 8: Start server
php artisan serve
```

### Method 3: Verify Before Setup ✅

```bash
# Check system requirements
VERIFY_SYSTEM.bat

# Then run setup
SETUP.bat
```

---

## 📋 Prerequisites

- ✅ PHP 8.2 or higher
- ✅ Composer
- ✅ PostgreSQL 12 or higher
- ✅ Windows OS (for .bat scripts)

---

## 🎯 Available Scripts

| Script | Purpose |
|--------|---------|
| `SETUP.bat` | Complete installation & setup |
| `START_SERVER.bat` | Start development server |
| `VERIFY_SYSTEM.bat` | Check system requirements |
| `COMPLETE_SETUP.bat` | Alternative setup script |

## 📝 Default Login Credentials

### Sample Student Login
- **Username**: `student001`
- **Password**: `password`
- **User Type**: Student

### Sample Teacher Login
- **Username**: `teacher001`
- **Password**: `password`
- **User Type**: Teacher

**Note**: You need to create these users first through database or admin panel.

## 🗂️ Project Structure

```
school-erp-system/
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AuthController.php
│   │       ├── StudentController.php
│   │       ├── TeacherController.php
│   │       └── MasterController.php
│   └── Models/
│       ├── Student.php
│       ├── Teacher.php
│       ├── Classes.php
│       ├── Section.php
│       ├── Subject.php
│       └── Session.php
├── database/
│   └── migrations/
│       ├── 2024_01_01_000001_create_sessions_table.php
│       ├── 2024_01_01_000002_create_classes_table.php
│       ├── 2024_01_01_000003_create_sections_table.php
│       ├── 2024_01_01_000004_create_subjects_table.php
│       ├── 2024_01_01_000005_create_class_subjects_table.php
│       ├── 2024_01_01_000006_create_students_table.php
│       ├── 2024_01_01_000007_create_teachers_table.php
│       └── 2024_01_01_000008_create_teacher_subjects_table.php
├── resources/
│   └── views/
│       ├── layouts/
│       │   └── app.blade.php
│       ├── auth/
│       │   └── login.blade.php
│       ├── student/
│       │   └── dashboard.blade.php
│       └── teacher/
│           └── dashboard.blade.php
└── routes/
    └── web.php
```

## 🎨 Technology Stack

- **Backend**: Laravel 11
- **Database**: PostgreSQL
- **Frontend**: Bootstrap 5, jQuery, AJAX
- **Notifications**: SweetAlert2
- **Icons**: Font Awesome 6

## 📋 Database Schema

### Students Table
- Personal Information (Name, DOB, Gender, etc.)
- Academic Information (Class, Section, Session)
- Guardian Information
- Login Credentials (Username, Password)
- Photo Upload Support

### Teachers Table
- Personal Information
- Professional Information (Qualification, Joining Date)
- Login Credentials
- Photo Upload Support

### Master Tables
- **Sessions**: Academic years with active status
- **Classes**: Class names and numeric values
- **Sections**: Sections per class with capacity
- **Subjects**: Subject details with codes

## 🔐 Security Features

- Password Hashing (bcrypt)
- CSRF Protection
- Multiple Authentication Guards (Student, Teacher)
- Session Management
- Input Validation

## 🎯 Key Features

### 1. Login System
- Beautiful login page with user type selection
- AJAX-based authentication
- SweetAlert notifications
- Password visibility toggle

### 2. Student Dashboard
- Personal information display
- Class and section details
- Attendance overview
- Notifications panel
- Profile management

### 3. Teacher Dashboard
- Professional information
- Assigned classes overview
- Today's schedule
- Student management access
- Grade book access

### 4. Admin Features
- Complete CRUD for all masters
- Student admission and management
- Teacher management
- Student promotion system
- Dynamic dropdowns (Class → Sections)

## 📱 Responsive Design

- Mobile-friendly interface
- Bootstrap 5 responsive grid
- Touch-friendly buttons
- Optimized for all screen sizes

## 🎨 UI/UX Features

- Modern gradient designs
- Smooth animations
- Card-based layouts
- Professional color scheme
- Icon-rich interface
- Loading overlays
- Beautiful alerts

## 🔄 AJAX Operations

All operations are AJAX-based:
- No page reloads
- Instant feedback
- Loading indicators
- Success/Error notifications
- Form validation

## 📊 Future Enhancements

- Attendance Management
- Fee Management
- Exam & Results Module
- Timetable Management
- Library Management
- Transport Management
- Hostel Management
- Reports & Analytics
- Parent Portal
- SMS/Email Notifications

## 🐛 Troubleshooting

### Database Connection Error
```bash
# Check PostgreSQL service
# Windows: Check Services
# Linux: sudo systemctl status postgresql
```

### Permission Issues
```bash
chmod -R 775 storage bootstrap/cache
```

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📞 Support

For any issues or questions, please check:
- Laravel Documentation: https://laravel.com/docs
- PostgreSQL Documentation: https://www.postgresql.org/docs/

## 📄 License

This project is open-source and available for educational purposes.

---

**Developed with ❤️ for School Management**
