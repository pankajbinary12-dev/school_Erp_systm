<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');
            $table->enum('type', ['bonafide', 'transfer', 'character', 'fee', 'migration']);
            $table->string('certificate_no')->unique();
            $table->date('issue_date');
            $table->text('content')->nullable();
            $table->string('qr_code')->nullable();
            $table->foreignId('issued_by')->nullable()->constrained('admins')->onDelete('set null');
            $table->enum('status', ['active', 'cancelled'])->default('active');
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['student_id', 'type']);
            $table->index('certificate_no');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
