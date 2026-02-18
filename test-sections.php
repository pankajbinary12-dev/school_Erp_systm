<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "=== Testing Sections ===\n\n";

// Test 1: Total sections
$total = App\Models\Section::count();
echo "Total Sections: $total\n";

// Test 2: Active sections
$active = App\Models\Section::where('is_active', 'Active')->count();
echo "Active Sections: $active\n\n";

// Test 3: Classes with section count
echo "Classes with sections:\n";
$classes = App\Models\Classes::withCount('sections')->get();
foreach($classes as $class) {
    echo "  {$class->class_name}: {$class->sections_count} sections\n";
}

echo "\n";

// Test 4: Sections for Class 1
echo "Sections for Class 1:\n";
$sections = App\Models\Section::where('class_id', 1)->where('is_active', 'Active')->get();
echo "Count: " . $sections->count() . "\n";
foreach($sections as $section) {
    echo "  - {$section->section_name} (ID: {$section->id})\n";
}

echo "\n";

// Test 5: Check what getSectionsByClass returns
echo "Testing getSectionsByClass method:\n";
$controller = new App\Http\Controllers\MasterController();
$response = $controller->getSectionsByClass(1);
$data = $response->getData(true);
echo "Response structure: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
