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
        Schema::create('wms_shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->unique()->constrained()->onDelete('cascade');
            $table->string('wms_shipment_id')->nullable()->unique()->comment('WMS 배송 ID');
            $table->string('tracking_number')->nullable()->comment('송장 번호');
            $table->string('status')->default('pending')->comment('배송 상태: pending, processing, shipped, delivered, failed');
            $table->string('carrier')->nullable()->comment('배송 업체');
            $table->timestamp('requested_at')->nullable()->comment('배송 요청 일시');
            $table->timestamp('shipped_at')->nullable()->comment('발송 일시');
            $table->timestamp('delivered_at')->nullable()->comment('배송 완료 일시');
            $table->text('notes')->nullable()->comment('비고');
            $table->json('wms_response')->nullable()->comment('WMS 응답 데이터');
            $table->timestamps();

            $table->index('status');
            $table->index('tracking_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('wms_shipments');
    }
};
