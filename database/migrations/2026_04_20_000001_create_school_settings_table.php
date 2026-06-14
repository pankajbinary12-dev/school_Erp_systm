<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('school_settings', function (Blueprint $table) {
            $table->id();
            $table->string('school_name', 200);
            $table->string('school_code', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('phone', 20)->nullable();
            $table->string('mobile', 20)->nullable();
            $table->text('address')->nullable();
            $table->string('city', 100)->nullable();
            $table->string('state', 100)->nullable();
            $table->string('pincode', 10)->nullable();
            $table->string('website', 200)->nullable();
            $table->string('logo')->nullable();
            $table->string('favicon')->nullable();
            $table->string('header_image')->nullable();
            $table->string('principal_name', 100)->nullable();
            $table->string('principal_signature')->nullable();
            $table->string('affiliation_no', 100)->nullable();
            $table->string('board', 100)->nullable();
            $table->text('about')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('school_settings');
    }
};
