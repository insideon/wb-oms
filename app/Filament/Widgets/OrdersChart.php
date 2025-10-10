<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class OrdersChart extends ChartWidget
{
    protected static ?string $heading = '일별 주문 추이';

    protected static ?int $sort = 2;

    protected static ?string $maxHeight = '300px';

    protected function getData(): array
    {
        $data = $this->getOrdersPerDay();

        return [
            'datasets' => [
                [
                    'label' => '주문 수',
                    'data' => $data['counts'],
                    'borderColor' => 'rgb(59, 130, 246)',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                ],
            ],
            'labels' => $data['labels'],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }

    private function getOrdersPerDay(): array
    {
        $days = collect();
        $counts = collect();

        for ($i = 13; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            $count = Order::whereDate('ordered_at', $date)->count();

            $days->push($date->format('m/d'));
            $counts->push($count);
        }

        return [
            'labels' => $days->toArray(),
            'counts' => $counts->toArray(),
        ];
    }
}
