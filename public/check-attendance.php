<?php
// Simple Attendance Checker - No Laravel Dependencies
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Attendance System Checker</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f5f5f5; }
        .box { background: white; padding: 20px; margin: 10px 0; border-radius: 5px; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; }
        .error { border-left-color: #dc3545; }
        .warning { border-left-color: #ffc107; }
        h2 { margin-top: 0; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 5px; cursor: pointer; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Attendance System Diagnostic</h1>

<?php
// Database connection
$host = '127.0.0.1';
$dbname = 'school_erp';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<div class="box success"><h2>✅ Database Connection: OK</h2></div>';
} catch(PDOException $e) {
    echo '<div class="box error"><h2>❌ Database Connection Failed</h2>';
    echo '<p>Error: ' . $e->getMessage() . '</p>';
    echo '<p><strong>Fix:</strong> Check database credentials in .env file</p></div>';
    exit;
}

// Check 1: Staff Members Table
echo '<div class="box">';
echo '<h2>1. Staff Members Check</h2>';
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM staff_members");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        echo "<p class='success'>✅ Found $count staff members</p>";
        
        // Show first 5 staff
        $stmt = $pdo->query("SELECT id, employee_id, first_name, last_name, designation FROM staff_members LIMIT 5");
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        echo '<tr><th>ID</th><th>Employee ID</th><th>Name</th><th>Designation</th></tr>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr>";
            echo "<td>{$row['id']}</td>";
            echo "<td>{$row['employee_id']}</td>";
            echo "<td>{$row['first_name']} {$row['last_name']}</td>";
            echo "<td>{$row['designation']}</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<p class="error">❌ No staff members found!</p>';
        echo '<p><strong>Fix:</strong> Add staff members first</p>';
    }
} catch(PDOException $e) {
    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
}
echo '</div>';

// Check 2: Staff Attendance Table
echo '<div class="box">';
echo '<h2>2. Staff Attendance Table Check</h2>';
try {
    $stmt = $pdo->query("SHOW COLUMNS FROM staff_attendance");
    echo '<p class="success">✅ Table exists</p>';
    echo '<p>Columns:</p><ul>';
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<li>{$row['Field']} ({$row['Type']})</li>";
    }
    echo '</ul>';
} catch(PDOException $e) {
    echo '<p class="error">❌ Table missing or error: ' . $e->getMessage() . '</p>';
    echo '<p><strong>Fix:</strong> Run migrations: php artisan migrate</p>';
}
echo '</div>';

// Check 3: Today's Attendance
echo '<div class="box">';
echo '<h2>3. Today\'s Attendance Records</h2>';
try {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT sa.*, sm.first_name, sm.last_name, sm.employee_id 
        FROM staff_attendance sa
        JOIN staff_members sm ON sa.staff_id = sm.id
        WHERE sa.attendance_date = ?
        ORDER BY sa.created_at DESC
    ");
    $stmt->execute([$today]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (count($records) > 0) {
        echo "<p class='success'>✅ Found " . count($records) . " attendance records for today</p>";
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        echo '<tr><th>Staff</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Remarks</th></tr>';
        foreach ($records as $row) {
            echo "<tr>";
            echo "<td>{$row['first_name']} {$row['last_name']} ({$row['employee_id']})</td>";
            echo "<td>{$row['check_in']}</td>";
            echo "<td>{$row['check_out']}</td>";
            echo "<td>{$row['status']}</td>";
            echo "<td>{$row['remarks']}</td>";
            echo "</tr>";
        }
        echo '</table>';
    } else {
        echo '<p class="warning">⚠️ No attendance records for today yet</p>';
    }
} catch(PDOException $e) {
    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
}
echo '</div>';

// Check 4: Test Insert
echo '<div class="box">';
echo '<h2>4. Test Manual Insert</h2>';

if (isset($_POST['test_insert'])) {
    try {
        $staff_id = $_POST['staff_id'];
        $today = date('Y-m-d');
        $time = date('H:i:s');
        
        // Check if already exists
        $stmt = $pdo->prepare("SELECT id FROM staff_attendance WHERE staff_id = ? AND attendance_date = ?");
        $stmt->execute([$staff_id, $today]);
        
        if ($stmt->fetch()) {
            echo '<p class="warning">⚠️ Attendance already exists for this staff today</p>';
        } else {
            // Insert
            $stmt = $pdo->prepare("
                INSERT INTO staff_attendance (
                    staff_id, attendance_date, status, check_in, 
                    expected_check_in, is_late, remarks, created_at, updated_at
                ) VALUES (?, ?, 'Present', ?, '09:00:00', 0, 'Test insert from diagnostic', NOW(), NOW())
            ");
            $stmt->execute([$staff_id, $today, $time]);
            
            echo '<p class="success">✅ Test insert successful! Attendance ID: ' . $pdo->lastInsertId() . '</p>';
            echo '<p>Refresh page to see in "Today\'s Attendance" section above</p>';
        }
    } catch(PDOException $e) {
        echo '<p class="error">❌ Insert failed: ' . $e->getMessage() . '</p>';
    }
}

// Show form
echo '<form method="POST">';
echo '<p>Select Staff to Test:</p>';
echo '<select name="staff_id" required>';
try {
    $stmt = $pdo->query("SELECT id, employee_id, first_name, last_name FROM staff_members LIMIT 10");
    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        echo "<option value='{$row['id']}'>{$row['employee_id']} - {$row['first_name']} {$row['last_name']}</option>";
    }
} catch(PDOException $e) {
    echo "<option>Error loading staff</option>";
}
echo '</select><br><br>';
echo '<button type="submit" name="test_insert" class="btn">Test Insert Attendance</button>';
echo '</form>';
echo '</div>';

// Check 5: Laravel Routes
echo '<div class="box">';
echo '<h2>5. Laravel Routes Check</h2>';
echo '<p>Testing if Laravel routes are accessible...</p>';
echo '<ul>';
echo '<li><a href="/admin/attendance/staff" target="_blank">Admin Staff Attendance</a></li>';
echo '<li><a href="/teacher/my-attendance" target="_blank">Teacher My Attendance</a></li>';
echo '<li><a href="/test-attendance.html" target="_blank">Test Page</a></li>';
echo '</ul>';
echo '</div>';

// Check 6: Permissions
echo '<div class="box">';
echo '<h2>6. File Permissions Check</h2>';
$paths = [
    'storage/logs' => is_writable('../storage/logs'),
    'storage/framework' => is_writable('../storage/framework'),
    'bootstrap/cache' => is_writable('../bootstrap/cache')
];

foreach ($paths as $path => $writable) {
    if ($writable) {
        echo "<p class='success'>✅ $path is writable</p>";
    } else {
        echo "<p class='error'>❌ $path is NOT writable</p>";
        echo "<p><strong>Fix:</strong> Run: chmod -R 775 $path</p>";
    }
}
echo '</div>';

?>

    <div class="box">
        <h2>📋 Summary & Next Steps</h2>
        <ol>
            <li>If all checks are ✅ green, system is working</li>
            <li>Try "Test Insert Attendance" button above</li>
            <li>If insert works, problem is in Laravel routes/controllers</li>
            <li>If insert fails, problem is in database/permissions</li>
        </ol>
        
        <h3>Common Fixes:</h3>
        <pre>
# Clear Laravel cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Fix permissions
chmod -R 775 storage bootstrap/cache

# Run migrations
php artisan migrate
        </pre>
    </div>

    <div class="box">
        <h2>🔗 Quick Links</h2>
        <ul>
            <li><a href="test-attendance.html">Simple Test Page</a></li>
            <li><a href="../admin/attendance/staff">Admin Attendance (requires login)</a></li>
            <li><a href="check-attendance.php">Refresh This Page</a></li>
        </ul>
    </div>

</body>
</html>
