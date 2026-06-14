<?php
// Quick Attendance Test - Direct Database Access
error_reporting(E_ALL);
ini_set('display_errors', 1);

header('Content-Type: text/html; charset=utf-8');

// Database config
$host = '127.0.0.1';
$dbname = 'school_erp';
$username = 'root';
$password = '';

?>
<!DOCTYPE html>
<html>
<head>
    <title>Quick Attendance Test</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .result { margin-top: 20px; padding: 15px; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Quick Attendance Test</h1>
        <p class="text-muted">Direct database test - no Laravel needed</p>

<?php
try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo '<div class="alert alert-success">✅ Database Connected</div>';
} catch(PDOException $e) {
    echo '<div class="alert alert-danger">❌ Database Error: ' . $e->getMessage() . '</div>';
    echo '<p><strong>Fix:</strong> Check database name, username, password in .env file</p>';
    exit;
}

// Get staff members
$stmt = $pdo->query("SELECT id, employee_id, first_name, last_name, designation FROM staff_members WHERE status = 'Active' ORDER BY id LIMIT 20");
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (empty($staff)) {
    echo '<div class="alert alert-warning">⚠️ No staff members found! Please add staff first.</div>';
    exit;
}

echo '<div class="alert alert-info">Found ' . count($staff) . ' staff members</div>';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['mark_attendance'])) {
    $staff_id = $_POST['staff_id'];
    $action = $_POST['action'];
    $today = date('Y-m-d');
    $time = date('H:i:s');
    
    try {
        if ($action === 'check_in') {
            // Check if already checked in
            $stmt = $pdo->prepare("SELECT id, check_in FROM staff_attendance WHERE staff_id = ? AND attendance_date = ?");
            $stmt->execute([$staff_id, $today]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($existing && $existing['check_in']) {
                echo '<div class="result error">❌ Already checked in today at ' . $existing['check_in'] . '</div>';
            } else {
                // Insert or update
                $is_late = ($time > '09:00:00') ? 1 : 0;
                $status = $is_late ? 'Late' : 'Present';
                
                if ($existing) {
                    // Update existing record
                    $stmt = $pdo->prepare("
                        UPDATE staff_attendance 
                        SET check_in = ?, status = ?, is_late = ?, remarks = 'Quick test check-in', updated_at = NOW()
                        WHERE id = ?
                    ");
                    $stmt->execute([$time, $status, $is_late, $existing['id']]);
                } else {
                    // Insert new record
                    $stmt = $pdo->prepare("
                        INSERT INTO staff_attendance (
                            staff_id, attendance_date, status, check_in, 
                            expected_check_in, is_late, remarks, created_at, updated_at
                        ) VALUES (?, ?, ?, ?, '09:00:00', ?, 'Quick test check-in', NOW(), NOW())
                    ");
                    $stmt->execute([$staff_id, $today, $status, $time, $is_late]);
                }
                
                echo '<div class="result success">✅ Check-in successful at ' . $time . ' (Status: ' . $status . ')</div>';
            }
        } else {
            // Check-out
            $stmt = $pdo->prepare("SELECT id, check_in, check_out FROM staff_attendance WHERE staff_id = ? AND attendance_date = ?");
            $stmt->execute([$staff_id, $today]);
            $existing = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$existing || !$existing['check_in']) {
                echo '<div class="result error">❌ No check-in found for today. Please check-in first.</div>';
            } elseif ($existing['check_out']) {
                echo '<div class="result error">❌ Already checked out today at ' . $existing['check_out'] . '</div>';
            } else {
                // Update check-out
                $stmt = $pdo->prepare("UPDATE staff_attendance SET check_out = ?, updated_at = NOW() WHERE id = ?");
                $stmt->execute([$time, $existing['id']]);
                
                // Calculate working hours
                $check_in_time = strtotime($existing['check_in']);
                $check_out_time = strtotime($time);
                $hours = round(($check_out_time - $check_in_time) / 3600, 2);
                
                echo '<div class="result success">✅ Check-out successful at ' . $time . '<br>Working hours: ' . $hours . ' hours</div>';
            }
        }
    } catch(PDOException $e) {
        echo '<div class="result error">❌ Database Error: ' . $e->getMessage() . '</div>';
    }
}

// Show today's attendance
$today = date('Y-m-d');
$stmt = $pdo->prepare("
    SELECT sa.*, sm.employee_id, sm.first_name, sm.last_name 
    FROM staff_attendance sa
    JOIN staff_members sm ON sa.staff_id = sm.id
    WHERE sa.attendance_date = ?
    ORDER BY sa.created_at DESC
");
$stmt->execute([$today]);
$today_attendance = $stmt->fetchAll(PDO::FETCH_ASSOC);

if (!empty($today_attendance)) {
    echo '<div class="card mt-4">';
    echo '<div class="card-header bg-success text-white"><h5>Today\'s Attendance (' . count($today_attendance) . ' records)</h5></div>';
    echo '<div class="card-body">';
    echo '<table class="table table-bordered">';
    echo '<tr><th>Staff</th><th>Check In</th><th>Check Out</th><th>Status</th><th>Working Hours</th></tr>';
    foreach ($today_attendance as $att) {
        $working_hours = '-';
        if ($att['check_in'] && $att['check_out']) {
            $diff = strtotime($att['check_out']) - strtotime($att['check_in']);
            $working_hours = round($diff / 3600, 2) . ' hrs';
        }
        echo '<tr>';
        echo '<td>' . $att['first_name'] . ' ' . $att['last_name'] . ' (' . $att['employee_id'] . ')</td>';
        echo '<td>' . ($att['check_in'] ?: '-') . '</td>';
        echo '<td>' . ($att['check_out'] ?: '-') . '</td>';
        echo '<td><span class="badge bg-' . ($att['status'] === 'Present' ? 'success' : ($att['status'] === 'Late' ? 'warning' : 'danger')) . '">' . $att['status'] . '</span></td>';
        echo '<td>' . $working_hours . '</td>';
        echo '</tr>';
    }
    echo '</table>';
    echo '</div></div>';
}
?>

        <div class="card mt-4">
            <div class="card-header bg-primary text-white">
                <h5>Mark Attendance</h5>
            </div>
            <div class="card-body">
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Select Staff</label>
                        <select name="staff_id" class="form-select" required>
                            <option value="">-- Select Staff --</option>
                            <?php foreach ($staff as $s): ?>
                                <option value="<?= $s['id'] ?>">
                                    <?= $s['employee_id'] ?> - <?= $s['first_name'] ?> <?= $s['last_name'] ?> (<?= $s['designation'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Action</label>
                        <select name="action" class="form-select" required>
                            <option value="check_in">Check In</option>
                            <option value="check_out">Check Out</option>
                        </select>
                    </div>
                    
                    <button type="submit" name="mark_attendance" class="btn btn-primary">
                        ✅ Mark Attendance Now
                    </button>
                </form>
            </div>
        </div>

        <div class="card mt-4">
            <div class="card-header bg-info text-white">
                <h5>Staff List</h5>
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr><th>ID</th><th>Employee ID</th><th>Name</th><th>Designation</th></tr>
                    <?php foreach ($staff as $s): ?>
                        <tr>
                            <td><?= $s['id'] ?></td>
                            <td><?= $s['employee_id'] ?></td>
                            <td><?= $s['first_name'] ?> <?= $s['last_name'] ?></td>
                            <td><?= $s['designation'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            </div>
        </div>

        <div class="alert alert-warning mt-4">
            <h5>📝 Instructions:</h5>
            <ol>
                <li>Select staff from dropdown</li>
                <li>Choose "Check In" or "Check Out"</li>
                <li>Click "Mark Attendance Now"</li>
                <li>Result will show above</li>
                <li>Today's attendance table will update automatically</li>
            </ol>
            <p><strong>Current Time:</strong> <?= date('Y-m-d H:i:s') ?></p>
        </div>
    </div>
</body>
</html>
