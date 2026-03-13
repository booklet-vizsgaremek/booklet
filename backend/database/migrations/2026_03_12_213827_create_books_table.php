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
             $table->uuid('id')->primary();
            $table->string('img', 300);
            $table->string('name', 255);
            $table->integer('author'); // ez majd foreignId lesz
            $table->integer('price');
            $table->integer('pages');
            $table->integer('xp');
            $table->integer('cr');
            $table->integer('in_storage');
            $table->enum('status', ['accepted', 'pending', 'rejected'])->default('pending');
            $table->integer('publisher_id'); // ez majd foreignId lesz
            $table->integer('genre_id');// ez majd foreignId lesz
            $table->timestamp('added_at')->nullable();
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
