<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Order;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WildberriesService
{
    protected string $apiKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.wildberries.api_key', '');
        $this->baseUrl = config('services.wildberries.base_url', 'https://suppliers-api.wildberries.ru');
    }

    /**
     * 와일드베리스에서 신규 주문 조회
     */
    public function fetchNewOrders(int $limit = 100): array
    {
        $startTime = microtime(true);
        $endpoint = '/api/v3/orders/new';

        try {
            $response = Http::withHeaders([
                'Authorization' => $this->apiKey,
            ])->get($this->baseUrl.$endpoint, [
                'limit' => $limit,
            ]);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();

                $this->logApiCall(
                    'GET',
                    $endpoint,
                    ['limit' => $limit],
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $data['orders'] ?? [];
            }

            $this->logApiCall(
                'GET',
                $endpoint,
                ['limit' => $limit],
                $response->body(),
                $response->status(),
                'failed',
                'API 요청 실패: '.$response->status(),
                $duration
            );

            return [];
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'GET',
                $endpoint,
                ['limit' => $limit],
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('Wildberries API Error: '.$e->getMessage());

            return [];
        }
    }

    /**
     * 주문 정보를 데이터베이스에 저장
     */
    public function saveOrder(array $orderData): ?Order
    {
        try {
            return Order::updateOrCreate(
                ['wb_order_id' => $orderData['id']],
                [
                    'customer_name' => $orderData['customer']['name'] ?? '',
                    'customer_address' => $orderData['customer']['address'] ?? '',
                    'customer_phone' => $orderData['customer']['phone'] ?? null,
                    'total_amount' => $orderData['total'] ?? 0,
                    'currency' => $orderData['currency'] ?? 'RUB',
                    'ordered_at' => $orderData['created_at'] ?? now(),
                    'raw_data' => $orderData,
                    'status' => 'pending',
                ]
            );
        } catch (\Exception $e) {
            Log::error('주문 저장 실패: '.$e->getMessage(), ['order_data' => $orderData]);

            return null;
        }
    }

    /**
     * API 호출 로그 기록
     */
    protected function logApiCall(
        string $method,
        string $endpoint,
        ?array $requestData,
        $responseData,
        ?int $statusCode,
        string $status,
        ?string $errorMessage,
        int $duration
    ): void {
        ApiLog::create([
            'service' => 'wildberries',
            'method' => $method,
            'endpoint' => $endpoint,
            'request_data' => $requestData ? json_encode($requestData) : null,
            'response_data' => is_array($responseData) ? json_encode($responseData) : $responseData,
            'status_code' => $statusCode,
            'status' => $status,
            'error_message' => $errorMessage,
            'duration_ms' => $duration,
        ]);
    }
}
