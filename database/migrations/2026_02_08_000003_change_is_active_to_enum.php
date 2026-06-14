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
            
            // Rename temp column to is_active (MariaDB compatible)
            DB::statement("ALTER TABLE {$tableName} CHANGE is_active_temp is_active VARCHAR(20) NOT NULL DEFAULT 'Active'");
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
            
            // Rename temp column (MariaDB compatible)
            DB::statement("ALTER TABLE {$tableName} CHANGE is_active_temp is_active TINYINT(1) NOT NULL DEFAULT 1");
        }
    }
};
