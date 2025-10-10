<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;

class OrderStatusChart extends ChartWidget
{
    protected static ?string $heading = '주문 상태별 분포';

    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected function getData(): array
    {
        $pending = Order::where('status', 'pending')->count();
        $translated = Order::where('status', 'translated')->count();
        $wmsSent = Order::where('status', 'wms_sent')->count();
        $shipped = Order::where('status', 'shipped')->count();
        $completed = Order::where('status', 'completed')->count();
        $cancelled = Order::where('status', 'cancelled')->count();

        return [
            'datasets' => [
                [
                    'label' => '주문 수',
                    'data' => [$pending, $translated, $wmsSent, $shipped, $completed, $cancelled],
                    'backgroundColor' => [
                        'rgb(148, 163, 184)', // pending
                        'rgb(59, 130, 246)',  // translated
                        'rgb(99, 102, 241)',  // wms_sent
                        'rgb(245, 158, 11)',  // shipped
                        'rgb(34, 197, 94)',   // completed
                        'rgb(239, 68, 68)',   // cancelled
                    ],
                ],
            ],
            'labels' => ['대기중', '번역완료', 'WMS 전송완료', '배송중', '완료', '취소'],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }

    protected function getOptions(): array
    {
        return [
            'indexAxis' => 'y',
            'maintainAspectRatio' => true,
            'aspectRatio' => 3,
            'scales' => [
                'x' => [
                    'beginAtZero' => true,
                    'ticks' => [
                        'precision' => 0,
                    ],
                ],
            ],
        ];
    }
}
