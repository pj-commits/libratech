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
            $table->string('book_code', 10)->unique();
            $table->string('title');
            $table->string('author')->nullable();
            $table->string('subject');
            $table->string('description', 250);
            $table->integer('grade_level');
            $table->string('competency')->nullable();
            $table->enum('type', ['pdf','link','physical']);
            $table->string('file_path')->nullable();
            $table->timestamps();
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
