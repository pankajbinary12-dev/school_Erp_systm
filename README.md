# 🎓 School ERP System

A comprehensive School Management System built with Laravel 11, featuring student management, attendance tracking, library management, and role-based access control.

## 🌐 Live Demo
**URL:** https://myschool.iympotech.com

## ✨ Features

### 📚 Student Management
- Student admission with auto-generated admission numbers
- Student promotion between classes
- Complete student profile management
- Student attendance tracking

### 👨‍🏫 Teacher Management
- Teacher registration and profile management
- Subject assignment
- Teacher attendance tracking

### 👥 Staff Management
- Staff member management
- Staff attendance tracking
- Leave management system

### 📖 Library Management
- Book categories management
- Books inventory with ISBN tracking
- Issue and return books to students/teachers/staff
- Fine calculation for overdue books
- Track available vs issued books

### 📊 Masters Data
- Academic sessions management
- Classes and sections
- Subjects management
- Class-section mapping

### 🔐 Security & Permissions
- Role-based access control (RBAC)
- 5 predefined roles: Super Admin, Admin, Teacher, Accountant, Librarian
- 32 granular permissions
- Assign permissions to roles
- Assign roles to users

### 📱 Additional Features
- Responsive design (mobile-friendly)
- AJAX-based operations (no page reload)
- DataTables for search, sort, and pagination
- SweetAlert2 for beautiful notifications
- Soft deletes for data recovery
- Real-time form validation

## 🛠️ Technology Stack

- **Backend:** Laravel 11.x
- **Frontend:** Bootstrap 5.3, jQuery 3.6
- **Database:** MySQL 8.0+
- **PHP:** 8.2+
- **Additional Libraries:**
  - DataTables 1.13
  - SweetAlert2 11.x
  - Font Awesome 6.x

## 📋 Requirements

- PHP >= 8.2
- Composer
- MySQL >= 8.0
- Node.js & NPM (optional, for asset compilation)
- Apache/Nginx web server

## 🚀 Installation

### 1. Clone Repository
```bash
git clone <repository-url>
cd school-erp-system
```

### 2. Install Dependencies
```bash
composer install
```

### 3. Environment Setup
```bash
cp .env.example .env
php artisan key:generate
```

### 4. Configure Database
Edit `.env` file:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=your_password
```

### 5. Run Migrations & Seeders
```bash
php artisan migrate
php artisan db:seed
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 6. Start Development Server
```bash
php artisan serve
```

Visit: http://127.0.0.1:8000

## 🔑 Default Login Credentials

### Admin Panel
```
URL: /admin/login
Username: admin
Password: admin123
```

### Student Portal
```
URL: /student/login
Username: student001
Password: password
```

### Teacher Portal
```
URL: /teacher/login
Username: teacher001
Password: password
```

## 📁 Project Structure

```
school-erp-system/
├── app/
│   ├── Http/Controllers/
│   │   ├── AdminController.php
│   │   ├── AttendanceController.php
│   │   ├── LibraryController.php
│   │   ├── MasterController.php
│   │   ├── RolePermissionController.php
│   │   ├── StaffController.php
│   │   ├── StudentController.php
│   │   └── TeacherController.php
│   ├── Models/
│   │   ├── Book.php
│   │   ├── BookCategory.php
│   │   ├── BookIssue.php
│   │   ├── Classes.php
│   │   ├── Permission.php
│   │   ├── Role.php
│   │   ├── Section.php
│   │   ├── StaffMember.php
│   │   ├── StaffLeave.php
│   │   ├── Student.php
│   │   ├── StudentAttendance.php
│   │   └── Teacher.php
│   └── Traits/
│       └── HasRolesAndPermissions.php
├── database/
│   ├── migrations/
│   └── seeders/
├── resources/
│   └── views/
│       └── admin/
│           ├── attendance/
│           ├── library/
│           ├── masters/
│           ├── settings/
│           ├── staff/
│           ├── students/
│           └── teachers/
└── routes/
    └── web.php
```

## 📖 Module Documentation

### Student Module
- **Admission:** Auto-generated admission numbers (format: YYYY0001)
- **Promotion:** Bulk promote students to next class
- **Attendance:** Manual and biometric attendance tracking

### Library Module
- **Categories:** Organize books by categories
- **Books:** Complete inventory with ISBN, author, publisher
- **Issue/Return:** Track book circulation with fine management

### Staff Module
- **Members:** Complete staff profile management
- **Attendance:** Daily attendance tracking
- **Leave:** Leave application and approval system

### Permissions Module
- **Roles:** 5 predefined roles with customizable permissions
- **Permissions:** 32 granular permissions across 8 modules
- **Assignment:** Easy role and permission assignment interface

## 🔧 Configuration

### Email Configuration
Edit `.env`:
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=your-email@gmail.com
MAIL_FROM_NAME="${APP_NAME}"
```

### Session Configuration
Default session lifetime: 120 minutes
Edit `config/session.php` to change.

## 🛡️ Security

- CSRF protection enabled
- XSS protection
- SQL injection prevention via Eloquent ORM
- Password hashing with bcrypt
- Role-based access control
- Soft deletes for data recovery

## 📊 Database Schema

### Key Tables  
- `admins` - Admin users  
- `students` - Student records 
- `teachers` - Teacher records   
- `staff_members` - Staff records   
- `classes` - Class definitions 
- `sections` - Section definitions 
- `subjects` - Subject definitions 
- `student_attendance` - Student attendance records
- `staff_attendance` - Staff attendance records
- `staff_leaves` - Staff leave applications
- `books` - Book inventory 
- `book_categories` - Book categories
- `book_issues` - Book issue/return records
- `roles` - User roles 
- `permissions` - System permissions 

## 🤝 Contributing  
  
1. Fork the repository  
2. Create your feature branch (`git checkout -b feature/AmazingFeature`)
3. Commit your changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

## 📝 License

This project is proprietary software. All rights reserved.

## 👨‍💻 Developer

Developed for production deployment at myschool.iympotech.com

## 📞 Support

For support and queries, please contact the system administrator.

---

**Version:** 1.0.0   
**Last Updated:** February 2026  
**Status:** Production Ready ✅  
