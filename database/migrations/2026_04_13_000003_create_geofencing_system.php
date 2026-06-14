<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Schools table with GPS coordinates
        Schema::create('schools', function (Blueprint $table) {
            $table->id();
            $table->string('school_code')->unique();
            $table->string('school_name');
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('country')->default('India');
            $table->string('pin_code');
            
            // GPS Coordinates
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->integer('geofence_radius')->default(100); // meters
            
            // Contact Info
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->string('principal_name')->nullable();
            
            // Settings
            $table->boolean('geofencing_enabled')->default(true);
            $table->boolean('strict_mode')->default(false); // Strict = no manual override
            $table->time('office_start_time')->default('09:00:00');
            $table->time('office_end_time')->default('17:00:00');
            
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
            $table->softDeletes();
        });

        // Link staff to schools
        Schema::table('staff_members', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->after('id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });

        // Link teachers to schools
        Schema::table('teachers', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->nullable()->after('id');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('set null');
        });

        // Attendance location logs
        Schema::create('attendance_location_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('attendance_id');
            $table->unsignedBigInteger('school_id');
            
            // Location Data
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->decimal('accuracy', 8, 2)->nullable(); // GPS accuracy in meters
            $table->decimal('distance_from_school', 8, 2); // Distance in meters
            
            // Device Info
            $table->string('device_type')->nullable(); // mobile, web, biometric
            $table->string('device_id')->nullable();
            $table->string('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            
            // Verification Status
            $table->boolean('location_verified')->default(false);
            $table->boolean('within_geofence')->default(false);
            $table->enum('verification_method', ['GPS', 'IP', 'Biometric', 'Manual'])->default('GPS');
            $table->text('verification_notes')->nullable();
            
            // AI Analysis
            $table->json('ai_analysis')->nullable(); // AI verification results
            $table->decimal('confidence_score', 5, 2)->nullable(); // 0-100
            $table->boolean('ai_approved')->default(true);
            $table->text('ai_flags')->nullable(); // Any suspicious activity
            
            $table->timestamps();
            
            $table->foreign('attendance_id')->references('id')->on('staff_attendance')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // Geofence violations log
        Schema::create('geofence_violations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->unsignedBigInteger('school_id');
            $table->date('violation_date');
            $table->time('violation_time');
            
            $table->decimal('attempted_latitude', 10, 8);
            $table->decimal('attempted_longitude', 11, 8);
            $table->decimal('distance_from_school', 8, 2);
            
            $table->enum('violation_type', ['Outside Geofence', 'GPS Spoofing', 'Suspicious Location', 'Multiple Locations'])->default('Outside Geofence');
            $table->text('violation_details')->nullable();
            
            $table->boolean('admin_notified')->default(false);
            $table->boolean('resolved')->default(false);
            $table->text('resolution_notes')->nullable();
            
            $table->timestamps();
            
            $table->foreign('staff_id')->references('id')->on('staff_members')->onDelete('cascade');
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });

        // Biometric devices
        Schema::create('biometric_devices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('school_id');
            $table->string('device_code')->unique();
            $table->string('device_name');
            $table->string('device_type'); // fingerprint, face_recognition, iris
            $table->string('manufacturer')->nullable();
            $table->string('model')->nullable();
            
            // Location
            $table->string('location_description'); // e.g., "Main Gate", "Staff Room"
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            
            // Connection
            $table->string('ip_address')->nullable();
            $table->string('mac_address')->nullable();
            $table->integer('port')->nullable();
            $table->string('api_endpoint')->nullable();
            $table->string('api_key')->nullable();
            
            $table->enum('status', ['Active', 'Inactive', 'Maintenance'])->default('Active');
            $table->timestamp('last_sync_at')->nullable();
            
            $table->timestamps();
            $table->softDeletes();
            
            $table->foreign('school_id')->references('id')->on('schools')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('biometric_devices');
        Schema::dropIfExists('geofence_violations');
        Schema::dropIfExists('attendance_location_logs');
        
        Schema::table('teachers', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
        
        Schema::table('staff_members', function (Blueprint $table) {
            $table->dropForeign(['school_id']);
            $table->dropColumn('school_id');
        });
        
        Schema::dropIfExists('schools');
    }
};
