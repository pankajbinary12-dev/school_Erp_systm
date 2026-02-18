<?php
// Simple test to check if classes exist
require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo "Testing Class-Section Module\n";
echo "============================\n\n";

// Check if Classes table has data
$classesCount = App\Models\Classes::count();
echo "Total Classes: $classesCount\n";

if ($classesCount > 0) {
    echo "\nClasses List:\n";
    App\Models\Classes::all()->each(function($class) {
        echo "  - ID: {$class->id}, Name: {$class->class_name}, Active: " . ($class->is_active ? 'Yes' : 'No') . "\n";
    });
} else {
    echo "\n⚠️ WARNING: No classes found in database!\n";
    echo "Please add classes first from Masters > Classes menu\n";
}

echo "\n";

// Check sections
$sectionsCount = App\Models\Section::count();
echo "Total Sections: $sectionsCount\n";

if ($sectionsCount > 0) {
    echo "\nSections List:\n";
    App\Models\Section::with('class')->get()->each(function($section) {
        $className = $section->class ? $section->class->class_name : 'No Class';
        echo "  - ID: {$section->id}, Name: {$section->section_name}, Class: {$className}\n";
    });
}

echo "\n✅ Test Complete!\n";
