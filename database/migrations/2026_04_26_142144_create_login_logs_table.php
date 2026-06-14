<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('login_logs', function (Blueprint $table) {
            $table->id();
            $table->string('user_type'); // admin, teacher, student, staff
            $table->unsignedBigInteger('user_id');
            $table->string('username');
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->string('device_type')->nullable(); // Desktop, Mobile, Tablet
            $table->string('browser')->nullable();
            $table->string('os')->nullable();
            $table->timestamp('login_at');
            $table->timestamp('logout_at')->nullable();
            $table->string('status')->default('success'); // success, failed
            $table->timestamps();
            
            $table->index(['user_type', 'user_id']);
            $table->index('login_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('login_logs');
    }
};
