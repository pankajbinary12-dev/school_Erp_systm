<?php
// Debug Attendance System
error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: text/html; charset=utf-8');
?>
<!DOCTYPE html>
<html>
<head>
    <title>Debug Attendance</title>
    <style>
        body { font-family: Arial; padding: 20px; background: #f0f0f0; }
        .step { background: white; padding: 15px; margin: 10px 0; border-left: 4px solid #007bff; }
        .success { border-left-color: #28a745; background: #d4edda; }
        .error { border-left-color: #dc3545; background: #f8d7da; }
        .warning { border-left-color: #ffc107; background: #fff3cd; }
        pre { background: #f8f9fa; padding: 10px; overflow-x: auto; }
        .btn { background: #007bff; color: white; padding: 10px 20px; border: none; cursor: pointer; margin: 5px; }
        .btn:hover { background: #0056b3; }
    </style>
</head>
<body>
    <h1>🔍 Attendance Debug Tool</h1>
    <p>Ye tool step-by-step check karega ki problem kaha hai</p>

<?php
$errors = [];
$success = [];

// Step 1: Database Connection
echo '<div class="step">';
echo '<h3>Step 1: Database Connection</h3>';
try {
    $pdo = new PDO("mysql:host=127.0.0.1;dbname=school_erp;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<p class="success">✅ Connected to database: school_erp</p>';
    $success[] = 'Database connected';
} catch(PDOException $e) {
    echo '<p class="error">❌ Connection failed: ' . $e->getMessage() . '</p>';
    $errors[] = 'Database connection failed';
    echo '</div></body></html>';
    exit;
}
echo '</div>';

// Step 2: Check Tables
echo '<div class="step">';
echo '<h3>Step 2: Check Required Tables</h3>';
$tables = ['staff_members', 'staff_attendance'];
foreach ($tables as $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        if ($stmt->rowCount() > 0) {
            echo "<p class='success'>✅ Table '$table' exists</p>";
            $success[] = "Table $table exists";
        } else {
            echo "<p class='error'>❌ Table '$table' NOT found</p>";
            $errors[] = "Table $table missing";
        }
    } catch(PDOException $e) {
        echo "<p class='error'>❌ Error checking table: " . $e->getMessage() . "</p>";
        $errors[] = "Error checking tables";
    }
}
echo '</div>';

// Step 3: Check Staff Members
echo '<div class="step">';
echo '<h3>Step 3: Check Staff Members</h3>';
try {
    $stmt = $pdo->query("SELECT COUNT(*) as count FROM staff_members WHERE status = 'Active'");
    $count = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    
    if ($count > 0) {
        echo "<p class='success'>✅ Found $count active staff members</p>";
        $success[] = "Staff members exist";
        
        // Show first 3
        $stmt = $pdo->query("SELECT id, employee_id, first_name, last_name FROM staff_members WHERE status = 'Active' LIMIT 3");
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse;">';
        echo '<tr><th>ID</th><th>Employee ID</th><th>Name</th></tr>';
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>{$row['id']}</td><td>{$row['employee_id']}</td><td>{$row['first_name']} {$row['last_name']}</td></tr>";
        }
        echo '</table>';
    } else {
        echo '<p class="error">❌ No active staff members found</p>';
        $errors[] = 'No staff members';
    }
} catch(PDOException $e) {
    echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
    $errors[] = 'Error checking staff';
}
echo '</div>';

// Step 4: Test INSERT
echo '<div class="step">';
echo '<h3>Step 4: Test Database INSERT</h3>';

if (isset($_POST['test_insert'])) {
    try {
        $test_id = 999999;
        $today = date('Y-m-d');
        
        // First, delete any test record
        $pdo->exec("DELETE FROM staff_attendance WHERE staff_id = $test_id");
        
        // Try to insert
        $stmt = $pdo->prepare("
            INSERT INTO staff_attendance (
                staff_id, attendance_date, status, check_in, 
                expected_check_in, is_late, remarks, created_at, updated_at
            ) VALUES (?, ?, 'Present', '09:00:00', '09:00:00', 0, 'DEBUG TEST', NOW(), NOW())
        ");
        
        $result = $stmt->execute([$test_id, $today]);
        
        if ($result) {
            $insert_id = $pdo->lastInsertId();
            echo "<p class='success'>✅ INSERT successful! Record ID: $insert_id</p>";
            $success[] = 'INSERT works';
            
            // Verify
            $stmt = $pdo->prepare("SELECT * FROM staff_attendance WHERE id = ?");
            $stmt->execute([$insert_id]);
            $record = $stmt->fetch(PDO::FETCH_ASSOC);
            
            echo '<p>Inserted record:</p>';
            echo '<pre>' . print_r($record, true) . '</pre>';
            
            // Clean up
            $pdo->exec("DELETE FROM staff_attendance WHERE id = $insert_id");
            echo '<p class="success">✅ Test record deleted (cleanup)</p>';
        } else {
            echo '<p class="error">❌ INSERT failed but no exception thrown</p>';
            $errors[] = 'INSERT failed silently';
        }
    } catch(PDOException $e) {
        echo '<p class="error">❌ INSERT Error: ' . $e->getMessage() . '</p>';
        echo '<pre>SQL State: ' . $e->getCode() . '</pre>';
        $errors[] = 'INSERT exception: ' . $e->getMessage();
    }
} else {
    echo '<form method="POST">';
    echo '<button type="submit" name="test_insert" class="btn">🧪 Run INSERT Test</button>';
    echo '</form>';
    echo '<p class="warning">⚠️ Click button above to test database INSERT</p>';
}
echo '</div>';

// Step 5: Real Attendance Insert
echo '<div class="step">';
echo '<h3>Step 5: Mark Real Attendance</h3>';

if (isset($_POST['mark_real'])) {
    $staff_id = $_POST['staff_id'];
    $today = date('Y-m-d');
    $time = date('H:i:s');
    
    try {
        // Check if exists
        $stmt = $pdo->prepare("SELECT id, check_in FROM staff_attendance WHERE staff_id = ? AND attendance_date = ?");
        $stmt->execute([$staff_id, $today]);
        $existing = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($existing && $existing['check_in']) {
            echo "<p class='warning'>⚠️ Already checked in today at {$existing['check_in']}</p>";
        } else {
            $is_late = ($time > '09:00:00') ? 1 : 0;
            $status = $is_late ? 'Late' : 'Present';
            
            if ($existing) {
                // Update
                $stmt = $pdo->prepare("UPDATE staff_attendance SET check_in = ?, status = ?, is_late = ?, updated_at = NOW() WHERE id = ?");
                $result = $stmt->execute([$time, $status, $is_late, $existing['id']]);
                $action = 'Updated';
                $record_id = $existing['id'];
            } else {
                // Insert
                $stmt = $pdo->prepare("
                    INSERT INTO staff_attendance (
                        staff_id, attendance_date, status, check_in, 
                        expected_check_in, is_late, remarks, created_at, updated_at
                    ) VALUES (?, ?, ?, ?, '09:00:00', ?, 'Debug tool entry', NOW(), NOW())
                ");
                $result = $stmt->execute([$staff_id, $today, $status, $time, $is_late]);
                $action = 'Inserted';
                $record_id = $pdo->lastInsertId();
            }
            
            if ($result) {
                echo "<p class='success'>✅ $action successfully! Record ID: $record_id</p>";
                echo "<p>Time: $time | Status: $status</p>";
                
                // Verify
                $stmt = $pdo->prepare("SELECT * FROM staff_attendance WHERE id = ?");
                $stmt->execute([$record_id]);
                $record = $stmt->fetch(PDO::FETCH_ASSOC);
                echo '<p>Saved record:</p>';
                echo '<pre>' . print_r($record, true) . '</pre>';
                
                $success[] = 'Real attendance marked';
            } else {
                echo '<p class="error">❌ Failed to save</p>';
                $errors[] = 'Save failed';
            }
        }
    } catch(PDOException $e) {
        echo '<p class="error">❌ Error: ' . $e->getMessage() . '</p>';
        echo '<pre>' . $e->getTraceAsString() . '</pre>';
        $errors[] = 'Exception: ' . $e->getMessage();
    }
}

// Show form
try {
    $stmt = $pdo->query("SELECT id, employee_id, first_name, last_name FROM staff_members WHERE status = 'Active' LIMIT 10");
    $staff_list = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($staff_list)) {
        echo '<form method="POST">';
        echo '<select name="staff_id" required>';
        foreach ($staff_list as $s) {
            echo "<option value='{$s['id']}'>{$s['employee_id']} - {$s['first_name']} {$s['last_name']}</option>";
        }
        echo '</select>';
        echo '<button type="submit" name="mark_real" class="btn">✅ Mark Attendance</button>';
        echo '</form>';
    }
} catch(PDOException $e) {
    echo '<p class="error">Error loading staff: ' . $e->getMessage() . '</p>';
}
echo '</div>';

// Step 6: View Today's Attendance
echo '<div class="step">';
echo '<h3>Step 6: Today\'s Attendance Records</h3>';
try {
    $today = date('Y-m-d');
    $stmt = $pdo->prepare("
        SELECT sa.*, sm.employee_id, sm.first_name, sm.last_name 
        FROM staff_attendance sa
        JOIN staff_members sm ON sa.staff_id = sm.id
        WHERE sa.attendance_date = ?
        ORDER BY sa.created_at DESC
    ");
    $stmt->execute([$today]);
    $records = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!empty($records)) {
        echo "<p class='success'>✅ Found " . count($records) . " records for today</p>";
        echo '<table border="1" cellpadding="5" style="border-collapse: collapse; width: 100%;">';
        echo '<tr><th>ID</th><th>Staff</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Remarks</th></tr>';
        foreach ($records as $r) {
            echo "<tr>";
            echo "<td>{$r['id']}</td>";
            echo "<td>{$r['first_name']} {$r['last_name']} ({$r['employee_id']})</td>";
            echo "<td>{$r['check_in']}</td>";
            echo "<td>{$r['check_out']}</td>";
            echo "<td>{$r['status']}</td>";
            echo "<td>{$r['remarks']}</td>";
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

// Summary
echo '<div class="step">';
echo '<h3>📊 Summary</h3>';
echo '<p><strong>Successful Checks:</strong> ' . count($success) . '</p>';
echo '<ul>';
foreach ($success as $s) {
    echo "<li style='color: green;'>✅ $s</li>";
}
echo '</ul>';

if (!empty($errors)) {
    echo '<p><strong>Errors Found:</strong> ' . count($errors) . '</p>';
    echo '<ul>';
    foreach ($errors as $e) {
        echo "<li style='color: red;'>❌ $e</li>";
    }
    echo '</ul>';
} else {
    echo '<p class="success">✅ No errors found! System is working.</p>';
}
echo '</div>';

// Quick fixes
echo '<div class="step">';
echo '<h3>🔧 Quick Fixes</h3>';
echo '<pre>';
echo "# If tables are missing:
php artisan migrate

# If permissions issue:
chmod -R 775 storage bootstrap/cache

# Clear cache:
php artisan config:clear
php artisan route:clear
php artisan cache:clear

# Check .env file:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=school_erp
DB_USERNAME=root
DB_PASSWORD=
";
echo '</pre>';
echo '</div>';
?>

    <div class="step">
        <h3>🔗 Other Tools</h3>
        <a href="quick-test.php" class="btn">Quick Test Page</a>
        <a href="check-attendance.php" class="btn">Full Checker</a>
        <a href="debug-attendance.php" class="btn">Refresh This Page</a>
    </div>

</body>
</html>
