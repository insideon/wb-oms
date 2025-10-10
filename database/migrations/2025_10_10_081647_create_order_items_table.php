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
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade');
            $table->unsignedBigInteger('product_id')->nullable();
            $table->string('product_name')->comment('상품명 (러시아어)');
            $table->string('product_name_translated')->nullable()->comment('상품명 (번역됨)');
            $table->string('sku')->nullable()->comment('상품 SKU');
            $table->integer('quantity')->comment('수량');
            $table->decimal('price', 10, 2)->comment('단가');
            $table->decimal('subtotal', 10, 2)->comment('소계');
            $table->timestamps();

            $table->index('order_id');
            $table->index('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
