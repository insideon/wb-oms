<?php

namespace App\Jobs;

use App\Services\WildberriesService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class FetchWildberriesOrders implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(WildberriesService $wildberriesService): void
    {
        Log::info('와일드베리스 주문 수집 시작');

        $orders = $wildberriesService->fetchNewOrders();

        foreach ($orders as $orderData) {
            $order = $wildberriesService->saveOrder($orderData);

            if ($order) {
                // 번역 작업 큐에 추가
                TranslateOrderData::dispatch($order);
            }
        }

        Log::info('와일드베리스 주문 수집 완료', ['count' => count($orders)]);
    }
}
