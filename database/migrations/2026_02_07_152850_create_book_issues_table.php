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
        Schema::create('book_issues', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_id')->constrained('books')->onDelete('cascade');
            $table->enum('member_type', ['Student', 'Teacher', 'Staff']);
            $table->unsignedBigInteger('member_id');
            $table->date('issue_date');
            $table->date('due_date');
            $table->date('return_date')->nullable();
            $table->enum('status', ['Issued', 'Returned', 'Overdue', 'Lost'])->default('Issued');
            $table->decimal('fine_amount', 10, 2)->default(0);
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_issues');
    }
};
