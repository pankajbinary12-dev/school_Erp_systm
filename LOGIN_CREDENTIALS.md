# 🔐 Login Credentials - School ERP System

## 📋 All User Credentials

---

## 👨‍🎓 STUDENT LOGINS

### Student 1
```
Username: student001
Password: password
User Type: Student
Name: Student1 Kumar
Class: Random (1-12)
```

### Student 2
```
Username: student002
Password: password
User Type: Student
Name: Student2 Kumar
Class: Random (1-12)
```

### Student 3
```
Username: student003
Password: password
User Type: Student
Name: Student3 Kumar
Class: Random (1-12)
```

### Student 4-10
```
Username: student004 to student010
Password: password
User Type: Student
```

**Total Students:** 10 sample students created

---

## 👨‍🏫 TEACHER LOGINS

### Teacher 1
```
Username: teacher001
Password: password
User Type: Teacher
Name: Teacher1 Singh
Qualification: M.Ed, B.Ed
```

### Teacher 2
```
Username: teacher002
Password: password
User Type: Teacher
Name: Teacher2 Singh
Qualification: M.Ed, B.Ed
```

### Teacher 3
```
Username: teacher003
Password: password
User Type: Teacher
Name: Teacher3 Singh
Qualification: M.Ed, B.Ed
```

### Teacher 4-5
```
Username: teacher004 to teacher005
Password: password
User Type: Teacher
```

**Total Teachers:** 5 sample teachers created

---

## 👨‍💼 ADMIN LOGIN (To Be Created)

**Note:** Admin login needs to be created separately or you can use teacher/student login to access admin features.

For now, you can create admin manually in database or add admin seeder.

---

## 📊 Sample Data Created

### Sessions
- ✅ 2024-2025 (Active)
- ✅ 2023-2024 (Inactive)

### Classes
- ✅ Class 1 to Class 12 (All active)

### Sections
- ✅ Section A, B, C for each class
- ✅ Total: 36 sections
- ✅ Capacity: 40 students each

### Subjects
- ✅ Mathematics (MATH101)
- ✅ Science (SCI101)
- ✅ English (ENG101)
- ✅ Hindi (HIN101)
- ✅ Social Studies (SST101)
- ✅ Computer Science (CS101)
- ✅ Physics (PHY101)
- ✅ Chemistry (CHEM101)
- ✅ Biology (BIO101)
- ✅ Physical Education (PE101)

---

## 🚀 How to Login

### Step 1: Start Server
```bash
php artisan serve
```

### Step 2: Open Browser
```
http://localhost:8000
```

### Step 3: Select User Type
- Click on "Student" or "Teacher" button

### Step 4: Enter Credentials
- Username: (from above list)
- Password: password

### Step 5: Click Login
- You will be redirected to respective dashboard

---

## 🔄 Reset Password (If Needed)

If you want to reset any password:

```bash
php artisan tinker
```

Then run:
```php
// For Student
$student = App\Models\Student::where('username', 'student001')->first();
$student->password = Hash::make('newpassword');
$student->save();

// For Teacher
$teacher = App\Models\Teacher::where('username', 'teacher001')->first();
$teacher->password = Hash::make('newpassword');
$teacher->save();
```

---

## 📝 Quick Test Credentials

**For Quick Testing:**

```
Student Login:
- Username: student001
- Password: password

Teacher Login:
- Username: teacher001
- Password: password
```

---

## ⚠️ Important Notes

1. **Default Password:** All users have password = "password"
2. **Change in Production:** Change all passwords before production use
3. **User Type:** Select correct user type (Student/Teacher) before login
4. **Case Sensitive:** Usernames are case-sensitive
5. **Active Status:** All seeded users are active by default

---

## 🔒 Security Recommendations

### For Production:
1. Change all default passwords
2. Implement password complexity rules
3. Add password reset functionality
4. Enable two-factor authentication
5. Add account lockout after failed attempts
6. Implement session timeout
7. Add audit logging

---

## 📞 Need Help?

If login not working:
1. Check database is seeded: `php artisan db:seed`
2. Clear cache: `php artisan cache:clear`
3. Check .env database credentials
4. Verify user exists in database
5. Check password is hashed correctly

---

## 🎯 Testing Checklist

- [ ] Student001 can login
- [ ] Teacher001 can login
- [ ] Student dashboard loads
- [ ] Teacher dashboard loads
- [ ] Logout works
- [ ] Wrong password shows error
- [ ] Wrong username shows error
- [ ] User type validation works

---

**Version:** 1.0.0  
**Last Updated:** February 2026  
**Status:** ✅ Ready to Use

---

**🎓 Happy Testing! All credentials are ready to use!**
