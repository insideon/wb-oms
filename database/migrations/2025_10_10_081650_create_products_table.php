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
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('wb_product_id')->unique()->comment('Wildberries 상품 ID');
            $table->string('name')->comment('상품명');
            $table->string('sku')->unique()->comment('상품 SKU');
            $table->text('description')->nullable()->comment('상품 설명');
            $table->integer('stock_quantity')->default(0)->comment('재고 수량');
            $table->decimal('price', 10, 2)->comment('판매 가격');
            $table->string('category')->nullable()->comment('카테고리');
            $table->string('barcode')->nullable()->comment('바코드');
            $table->json('images')->nullable()->comment('상품 이미지 URL 배열');
            $table->boolean('is_active')->default(true)->comment('활성 여부');
            $table->timestamp('last_synced_at')->nullable()->comment('마지막 동기화 시간');
            $table->timestamps();
            $table->softDeletes();

            $table->index('sku');
            $table->index('is_active');
            $table->index('stock_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
