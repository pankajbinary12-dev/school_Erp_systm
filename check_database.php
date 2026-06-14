<?php
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

try {
    $pdo = DB::connection()->getPdo();
    echo "✅ Database Connection: SUCCESS\n\n";
    
    // Get database name
    $dbName = DB::connection()->getDatabaseName();
    echo "📊 Database Name: {$dbName}\n\n";
    
    // Get all tables
    $tables = DB::select('SHOW TABLES');
    echo "📋 Total Tables: " . count($tables) . "\n\n";
    
    echo "Tables List:\n";
    echo str_repeat("=", 50) . "\n";
    
    $tableKey = "Tables_in_{$dbName}";
    foreach ($tables as $table) {
        $tableName = $table->$tableKey;
        
        // Count records in each important table
        if (in_array($tableName, ['teachers', 'staff_members', 'students', 'admins', 'enquiries'])) {
            $count = DB::table($tableName)->count();
            echo "✓ {$tableName} - {$count} records\n";
        } else {
            echo "  {$tableName}\n";
        }
    }
    
    echo "\n" . str_repeat("=", 50) . "\n";
    echo "\n🌐 phpMyAdmin URL: http://localhost/phpmyadmin\n";
    echo "📂 Database: {$dbName}\n";
    
} catch (Exception $e) {
    echo "❌ Database Connection FAILED!\n";
    echo "Error: " . $e->getMessage() . "\n";
}
