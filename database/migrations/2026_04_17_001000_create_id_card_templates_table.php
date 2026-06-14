<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('id_card_templates', function (Blueprint $table) {
            $table->id();
            $table->string('template_name', 100);
            $table->string('border_style', 50); // modern, classic, colorful, minimal
            $table->string('border_color', 20)->default('#667eea');
            $table->string('background_color', 20)->default('#ffffff');
            $table->string('text_color', 20)->default('#000000');
            $table->string('header_bg_color', 20)->default('#667eea');
            $table->boolean('show_logo')->default(true);
            $table->boolean('show_qr_code')->default(true);
            $table->boolean('show_barcode')->default(false);
            $table->enum('is_active', ['Active', 'Inactive'])->default('Active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('id_card_templates');
    }
};
