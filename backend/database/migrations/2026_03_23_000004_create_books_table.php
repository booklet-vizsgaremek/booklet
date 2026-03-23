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
            $table->string('img_path', 255);
            $table->string('name', 255);
            $table->foreignUuid('author_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('price');
            $table->integer('pages');
            $table->integer('stock');
            $table->foreignUuid('publisher_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('genre_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
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
