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
        Schema::create('receipts', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('user_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->dateTime('date');
        });

        Schema::create('books_receipts', function (Blueprint $table) {
            $table->foreignUuid('receipt_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid('book_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer('quantity');
            $table->integer('price_at_purchase');
            $table->primary(['receipt_id', 'book_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('books_receipts');
        Schema::dropIfExists('receipts');
    }
};
