<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // List of tables with is_active column
        $tables = [
            'admins',
            'sessions',
            'classes',
            'sections',
            'subjects',
        ];

        foreach ($tables as $tableName) {
            // Add temporary column
            Schema::table($tableName, function (Blueprint $table) {
                $table->string('is_active_temp', 20)->nullable();
            });
            
            // Copy data: true -> 'Active', false -> 'Inactive'
            DB::table($tableName)->where('is_active', true)->update(['is_active_temp' => 'Active']);
            DB::table($tableName)->where('is_active', false)->update(['is_active_temp' => 'Inactive']);
            DB::table($tableName)->whereNull('is_active')->update(['is_active_temp' => 'Active']);
            
            // Drop old column
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
            
            // Rename temp column to is_active and set default
            DB::statement("ALTER TABLE {$tableName} RENAME COLUMN is_active_temp TO is_active");
            DB::statement("ALTER TABLE {$tableName} ALTER COLUMN is_active SET DEFAULT 'Active'");
            DB::statement("ALTER TABLE {$tableName} ALTER COLUMN is_active SET NOT NULL");
            
            // Add check constraint
            DB::statement("ALTER TABLE {$tableName} ADD CONSTRAINT {$tableName}_is_active_check CHECK (is_active IN ('Active', 'Inactive'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'admins',
            'sessions',
            'classes',
            'sections',
            'subjects',
        ];

        foreach ($tables as $tableName) {
            // Drop check constraint
            DB::statement("ALTER TABLE {$tableName} DROP CONSTRAINT IF EXISTS {$tableName}_is_active_check");
            
            // Add temporary boolean column
            Schema::table($tableName, function (Blueprint $table) {
                $table->boolean('is_active_temp')->default(true);
            });
            
            // Copy data: 'Active' -> true, 'Inactive' -> false
            DB::table($tableName)->where('is_active', 'Active')->update(['is_active_temp' => true]);
            DB::table($tableName)->where('is_active', 'Inactive')->update(['is_active_temp' => false]);
            
            // Drop old column
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('is_active');
            });
            
            // Rename temp column
            DB::statement("ALTER TABLE {$tableName} RENAME COLUMN is_active_temp TO is_active");
        }
    }
};
