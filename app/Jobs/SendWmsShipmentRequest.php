<?php

namespace App\Jobs;

use App\Models\Order;
use App\Services\WmsService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class SendWmsShipmentRequest implements ShouldQueue
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
    public function handle(WmsService $wmsService): void
    {
        Log::info('WMS 배송 요청 시작', ['order_id' => $this->order->id]);

        $shipment = $wmsService->requestShipment($this->order);

        if ($shipment) {
            Log::info('WMS 배송 요청 성공', [
                'order_id' => $this->order->id,
                'shipment_id' => $shipment->id,
            ]);
        } else {
            Log::error('WMS 배송 요청 실패', ['order_id' => $this->order->id]);
        }
    }
}
