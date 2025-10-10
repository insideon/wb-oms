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
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('wb_order_id')->unique()->comment('Wildberries 주문 ID');
            $table->string('customer_name')->comment('고객명 (러시아어)');
            $table->string('customer_name_translated')->nullable()->comment('고객명 (번역됨)');
            $table->text('customer_address')->comment('배송 주소 (러시아어)');
            $table->text('customer_address_translated')->nullable()->comment('배송 주소 (번역됨)');
            $table->string('customer_phone')->nullable()->comment('고객 전화번호');
            $table->string('status')->default('pending')->comment('주문 상태: pending, translated, wms_sent, shipped, completed, cancelled');
            $table->decimal('total_amount', 10, 2)->comment('주문 총액');
            $table->string('currency', 3)->default('RUB')->comment('통화');
            $table->timestamp('ordered_at')->nullable()->comment('주문 일시');
            $table->json('raw_data')->nullable()->comment('와일드베리스 원본 데이터');
            $table->timestamps();
            $table->softDeletes();

            $table->index('status');
            $table->index('ordered_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
