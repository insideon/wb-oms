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
        Schema::create('api_logs', function (Blueprint $table) {
            $table->id();
            $table->string('service')->comment('API 서비스명: wildberries, wms, translation');
            $table->string('method')->comment('HTTP 메서드');
            $table->string('endpoint')->comment('API 엔드포인트');
            $table->text('request_data')->nullable()->comment('요청 데이터');
            $table->text('response_data')->nullable()->comment('응답 데이터');
            $table->integer('status_code')->nullable()->comment('HTTP 상태 코드');
            $table->string('status')->comment('처리 상태: success, failed, error');
            $table->text('error_message')->nullable()->comment('에러 메시지');
            $table->integer('duration_ms')->nullable()->comment('응답 시간 (ms)');
            $table->timestamps();

            $table->index('service');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('api_logs');
    }
};
