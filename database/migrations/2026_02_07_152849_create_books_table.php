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
        Schema::create('books', function (Blueprint $table) {
            $table->id();
            $table->string('book_no')->unique();
            $table->string('title');
            $table->foreignId('category_id')->constrained('book_categories')->onDelete('cascade');
            $table->string('author');
            $table->string('publisher')->nullable();
            $table->string('isbn')->nullable()->unique();
            $table->year('publication_year')->nullable();
            $table->integer('quantity')->default(1);
            $table->integer('available_quantity')->default(1);
            $table->decimal('price', 10, 2)->nullable();
            $table->string('rack_no')->nullable();
            $table->text('description')->nullable();
            $table->enum('status', ['Available', 'Issued', 'Lost', 'Damaged'])->default('Available');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
