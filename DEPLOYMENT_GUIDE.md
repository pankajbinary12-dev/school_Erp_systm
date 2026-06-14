# 🚀 School ERP System - Deployment Guide

## Domain: myschool.iympotech.com

## Pre-Deployment Checklist:

### 1. Environment Configuration
```bash
# Copy .env.example to .env (if not exists)
cp .env.example .env

# Update .env file with production settings:
APP_NAME="School ERP System"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://myschool.iympotech.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=your_database_name
DB_USERNAME=your_database_user
DB_PASSWORD=your_database_password
```

### 2. Install Dependencies
```bash
composer install --optimize-autoloader --no-dev
```

### 3. Generate Application Key
```bash
php artisan key:generate
```

### 4. Run Migrations
```bash
php artisan migrate --force
```

### 5. Seed Database
```bash
php artisan db:seed
php artisan db:seed --class=RolesAndPermissionsSeeder
```

### 6. Optimize for Production
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 7. Set Permissions
```bash
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### 8. Configure Web Server

#### Apache (.htaccess already configured)
Point document root to: `/public`

#### Nginx Configuration
```nginx
server {
    listen 80;
    server_name myschool.iympotech.com;
    root /path/to/school-erp-system/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;

    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

### 9. SSL Certificate (Let's Encrypt)
```bash
sudo certbot --nginx -d myschool.iympotech.com
```

## Default Login Credentials:

### Admin Panel
```
URL: https://myschool.iympotech.com/admin/login
Username: admin
Password: admin123
```

### Student Login
```
URL: https://myschool.iympotech.com/student/login
Username: student001
Password: password
```

### Teacher Login
```
URL: https://myschool.iympotech.com/teacher/login
Username: teacher001
Password: password
```

## Post-Deployment Tasks:

1. ✅ Change default admin password
2. ✅ Update school information in settings
3. ✅ Configure email settings for notifications
4. ✅ Set up backup schedule
5. ✅ Test all modules:
   - Student Management
   - Teacher Management
   - Staff Management
   - Attendance (Student & Staff)
   - Library Management
   - Permissions & Roles
   - Class & Section Management

## Modules Available:

### 1. Student Management
- Admission
- Promotion
- Student List
- Attendance

### 2. Teacher Management
- Add/Edit Teachers
- Subject Assignment
- Teacher List

### 3. Staff Management
- Staff Members
- Staff Attendance
- Leave Management

### 4. Library Management
- Book Categories
- Books Inventory
- Issue & Return Books

### 5. Masters
- Sessions
- Classes
- Sections
- Subjects
- Class-Section Mapping

### 6. Settings
- Roles Management
- Permissions Management
- Assign Permissions to Roles
- Assign Roles to Users

## Security Recommendations:

1. Change all default passwords immediately
2. Enable HTTPS/SSL
3. Set APP_DEBUG=false in production
4. Regular database backups
5. Keep Laravel and dependencies updated
6. Monitor error logs: `storage/logs/laravel.log`
7. Implement rate limiting for login attempts
8. Use strong database passwords

## Maintenance Commands:

### Clear Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

### View Logs
```bash
tail -f storage/logs/laravel.log
```

### Database Backup
```bash
mysqldump -u username -p database_name > backup_$(date +%Y%m%d).sql
```

## Troubleshooting:

### Issue: 500 Internal Server Error
- Check storage permissions: `chmod -R 755 storage`
- Check .env configuration
- Check error logs: `storage/logs/laravel.log`

### Issue: Database Connection Error
- Verify database credentials in .env
- Ensure database exists
- Check MySQL service is running

### Issue: Assets Not Loading
- Run: `php artisan storage:link`
- Check public folder permissions

## Support:

For issues or questions, check:
- Laravel Documentation: https://laravel.com/docs
- Error Logs: `storage/logs/laravel.log`

## Version Information:

- Laravel: 11.x
- PHP: 8.2+
- MySQL: 8.0+
- Bootstrap: 5.3
- jQuery: 3.6
- DataTables: 1.13
- SweetAlert2: 11.x

---

**Deployment Date:** [Add Date]
**Deployed By:** [Add Name]
**Domain:** myschool.iympotech.com
