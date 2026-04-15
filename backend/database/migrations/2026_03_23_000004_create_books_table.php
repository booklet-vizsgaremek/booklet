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
            $table->string('img_path', 255)->nullable();
            $table->string('title', 255);
            $table->integer('price');
            $table->integer('pages');
            $table->integer('release_year');
            $table->integer('stock');
            $table->foreignUuid('publisher_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('genre_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('author_book', function (Blueprint $table) {
            $table->foreignUuid('author_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('book_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['author_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('author_book');
        Schema::dropIfExists('books');
    }
};
