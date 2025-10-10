<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\TranslationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class TranslateOrderData implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(public Order $order)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(TranslationService $translationService): void
    {
        Log::info('주문 번역 시작', ['order_id' => $this->order->id]);

        // 고객명 번역
        $customerNameTranslated = $translationService->translate($this->order->customer_name, 'ko', 'ru');

        // 배송 주소 번역
        $customerAddressTranslated = $translationService->translate($this->order->customer_address, 'ko', 'ru');

        // 주문 아이템 번역
        foreach ($this->order->items as $item) {
            $productNameTranslated = $translationService->translate($item->product_name, 'ko', 'ru');

            $item->update([
                'product_name_translated' => $productNameTranslated,
            ]);
        }

        // 번역 결과 저장
        $this->order->update([
            'customer_name_translated' => $customerNameTranslated,
            'customer_address_translated' => $customerAddressTranslated,
            'status' => 'translated',
        ]);

        Log::info('주문 번역 완료', ['order_id' => $this->order->id]);
    }
}
