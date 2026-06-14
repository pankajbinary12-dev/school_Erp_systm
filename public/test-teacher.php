<?php
// Simple Teacher Panel Test
error_reporting(E_ALL);
ini_set('display_errors', 1);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Teacher Panel Test</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; }
        .success { border-left: 4px solid #28a745; }
        .error { border-left: 4px solid #dc3545; }
        .info { border-left: 4px solid #17a2b8; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        a { color: #007bff; text-decoration: none; }
        a:hover { text-decoration: underline; }
    </style>
</head>
<body>
    <h1>🧪 Teacher Panel Test</h1>

    <div class="box info">
        <h3>Test Links</h3>
        <p>Click these links to test each page:</p>
        <ul>
            <li><a href="/teacher/dashboard" target="_blank">Dashboard</a></li>
            <li><a href="/teacher/profile" target="_blank">My Profile</a></li>
            <li><a href="/teacher/students" target="_blank">My Students</a></li>
            <li><a href="/teacher/subjects" target="_blank">My Subjects</a></li>
            <li><a href="/teacher/my-attendance" target="_blank">My Attendance</a></li>
            <li><a href="/teacher/assignments" target="_blank">Assignments</a></li>
            <li><a href="/teacher/gradebook" target="_blank">Grade Book</a></li>
        </ul>
        <p><strong>Note:</strong> You must be logged in as teacher first!</p>
    </div>

    <div class="box info">
        <h3>Login First</h3>
        <p>If not logged in, <a href="/login" target="_blank">click here to login</a></p>
        <pre>
Username: teacher001
Password: password
        </pre>
    </div>

    <div class="box success">
        <h3>✅ Files Check</h3>
        <?php
        $files = [
            'resources/views/teacher/profile/index.blade.php',
            'resources/views/teacher/students/index.blade.php',
            'resources/views/teacher/subjects/index.blade.php',
            'resources/views/teacher/assignments/index.blade.php',
            'resources/views/teacher/gradebook/index.blade.php',
            'resources/views/teacher/partials/sidebar.blade.php',
            'resources/views/teacher/partials/navbar.blade.php'
        ];
        
        echo '<ul>';
        foreach ($files as $file) {
            $path = '../' . $file;
            if (file_exists($path)) {
                echo "<li style='color: green;'>✅ $file</li>";
            } else {
                echo "<li style='color: red;'>❌ $file (MISSING!)</li>";
            }
        }
        echo '</ul>';
        ?>
    </div>

    <div class="box info">
        <h3>📋 Routes Check</h3>
        <p>Run this command to see all teacher routes:</p>
        <pre>php artisan route:list | findstr teacher</pre>
    </div>

    <div class="box info">
        <h3>🔧 If Pages Not Working:</h3>
        <ol>
            <li>Make sure you're logged in as teacher</li>
            <li>Clear cache:
                <pre>php artisan view:clear
php artisan route:clear
php artisan cache:clear</pre>
            </li>
            <li>Check Laravel logs: <code>storage/logs/laravel.log</code></li>
            <li>Check browser console (F12) for JavaScript errors</li>
        </ol>
    </div>

    <div class="box error">
        <h3>❌ Common Issues:</h3>
        <ul>
            <li><strong>404 Error:</strong> Route not found - run <code>php artisan route:clear</code></li>
            <li><strong>500 Error:</strong> Check <code>storage/logs/laravel.log</code></li>
            <li><strong>Blank Page:</strong> Check browser console (F12)</li>
            <li><strong>Not Logged In:</strong> Go to <a href="/login">/login</a> first</li>
        </ul>
    </div>

    <div class="box success">
        <h3>✅ Quick Fix Commands:</h3>
        <pre>
# Clear all caches
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Check routes
php artisan route:list | findstr teacher

# Check for errors
tail -f storage/logs/laravel.log
        </pre>
    </div>

</body>
</html>
