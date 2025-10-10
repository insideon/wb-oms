<?php

namespace App\Services;

use App\Models\ApiLog;
use App\Models\Order;
use App\Models\WmsShipment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WmsService
{
    protected string $apiKey;

    protected string $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('services.wms.api_key', '');
        $this->baseUrl = config('services.wms.base_url', '');
    }

    /**
     * WMS에 배송 요청 전송
     */
    public function requestShipment(Order $order): ?WmsShipment
    {
        $startTime = microtime(true);
        $endpoint = '/api/shipments';

        try {
            $shipmentData = [
                'order_id' => $order->wb_order_id,
                'customer_name' => $order->customer_name_translated ?? $order->customer_name,
                'customer_address' => $order->customer_address_translated ?? $order->customer_address,
                'customer_phone' => $order->customer_phone,
                'items' => $order->items->map(function ($item) {
                    return [
                        'sku' => $item->sku,
                        'product_name' => $item->product_name_translated ?? $item->product_name,
                        'quantity' => $item->quantity,
                    ];
                })->toArray(),
            ];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
                'Content-Type' => 'application/json',
            ])->post($this->baseUrl.$endpoint, $shipmentData);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();

                $shipment = WmsShipment::create([
                    'order_id' => $order->id,
                    'wms_shipment_id' => $data['shipment_id'] ?? null,
                    'status' => 'processing',
                    'requested_at' => now(),
                    'wms_response' => $data,
                ]);

                $order->update(['status' => 'wms_sent']);

                $this->logApiCall(
                    'POST',
                    $endpoint,
                    $shipmentData,
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $shipment;
            }

            $this->logApiCall(
                'POST',
                $endpoint,
                $shipmentData,
                $response->body(),
                $response->status(),
                'failed',
                'WMS 요청 실패: '.$response->status(),
                $duration
            );

            return null;
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'POST',
                $endpoint,
                $shipmentData ?? [],
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('WMS API Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * WMS에서 배송 상태 조회
     */
    public function getShipmentStatus(string $wmsShipmentId): ?array
    {
        $startTime = microtime(true);
        $endpoint = "/api/shipments/{$wmsShipmentId}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
            ])->get($this->baseUrl.$endpoint);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();

                $this->logApiCall(
                    'GET',
                    $endpoint,
                    ['wms_shipment_id' => $wmsShipmentId],
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $data;
            }

            $this->logApiCall(
                'GET',
                $endpoint,
                ['wms_shipment_id' => $wmsShipmentId],
                $response->body(),
                $response->status(),
                'failed',
                'Status check failed',
                $duration
            );

            return null;
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'GET',
                $endpoint,
                ['wms_shipment_id' => $wmsShipmentId],
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('WMS Status Check Error: '.$e->getMessage());

            return null;
        }
    }

    /**
     * WMS에서 재고 정보 조회
     */
    public function getStockInfo(string $sku): ?array
    {
        $startTime = microtime(true);
        $endpoint = "/api/stock/{$sku}";

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer '.$this->apiKey,
            ])->get($this->baseUrl.$endpoint);

            $duration = (int) ((microtime(true) - $startTime) * 1000);

            if ($response->successful()) {
                $data = $response->json();

                $this->logApiCall(
                    'GET',
                    $endpoint,
                    ['sku' => $sku],
                    $data,
                    $response->status(),
                    'success',
                    null,
                    $duration
                );

                return $data;
            }

            $this->logApiCall(
                'GET',
                $endpoint,
                ['sku' => $sku],
                $response->body(),
                $response->status(),
                'failed',
                'Stock check failed',
                $duration
            );

            return null;
        } catch (\Exception $e) {
            $duration = (int) ((microtime(true) - $startTime) * 1000);

            $this->logApiCall(
                'GET',
                $endpoint,
                ['sku' => $sku],
                null,
                null,
                'error',
                $e->getMessage(),
                $duration
            );

            Log::error('WMS Stock Check Error: '.$e->getMessage());

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
            'service' => 'wms',
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
