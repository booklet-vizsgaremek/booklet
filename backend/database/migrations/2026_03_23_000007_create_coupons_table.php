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
        Schema::create('coupons', function (Blueprint $table) {
            $table->uuid("id")->primary();
            $table->foreignUuid("book_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("genre_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignUuid("user_id")->nullable()->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->integer("discount");
            $table->dateTime("starts_at");
            $table->dateTime("ends_at");
            $table->string("code")->nullable();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('coupons_receipts', function (Blueprint $table) {
            $table->foreignUuid('receipt_id')->constrained();
            $table->foreignUuid('coupon_id')->constrained()->cascadeOnDelete()->cascadeOnUpdate();
            $table->primary(['receipt_id', 'coupon_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coupons_receipts');
        Schema::dropIfExists('coupons');
    }
};
