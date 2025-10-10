<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class StatsOverview extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $today = Carbon::today();
        $thisWeekStart = Carbon::now()->startOfWeek();
        $thisMonthStart = Carbon::now()->startOfMonth();

        $todayOrders = Order::whereDate('ordered_at', $today)->count();
        $weekOrders = Order::where('ordered_at', '>=', $thisWeekStart)->count();
        $monthOrders = Order::where('ordered_at', '>=', $thisMonthStart)->count();

        $todayRevenue = Order::whereDate('ordered_at', $today)->sum('total_amount');
        $weekRevenue = Order::where('ordered_at', '>=', $thisWeekStart)->sum('total_amount');
        $monthRevenue = Order::where('ordered_at', '>=', $thisMonthStart)->sum('total_amount');

        $avgOrderValue = Order::where('ordered_at', '>=', $thisMonthStart)->avg('total_amount') ?? 0;

        $pendingOrders = Order::where('status', 'pending')->count();
        $processingOrders = Order::whereIn('status', ['translated', 'wms_sent'])->count();

        return [
            Stat::make('금일 주문', $todayOrders)
                ->description('오늘 접수된 주문')
                ->descriptionIcon('heroicon-o-shopping-cart')
                ->color('success')
                ->chart([7, 12, 15, 18, 22, 25, $todayOrders]),
            Stat::make('월간 주문', $monthOrders)
                ->description('이번 달 총 주문')
                ->descriptionIcon('heroicon-o-calendar-days')
                ->color('primary'),
            Stat::make('월간 매출', '₽ '.number_format($monthRevenue, 2))
                ->description('이번 달 총 매출')
                ->descriptionIcon('heroicon-o-banknotes')
                ->color('info'),
            Stat::make('평균 주문 금액', '₽ '.number_format($avgOrderValue, 2))
                ->description('월간 평균')
                ->descriptionIcon('heroicon-o-calculator')
                ->color('warning'),
            Stat::make('처리 대기', $pendingOrders)
                ->description('번역 대기중인 주문')
                ->descriptionIcon('heroicon-o-clock')
                ->color('danger'),
            Stat::make('처리중', $processingOrders)
                ->description('번역완료 및 WMS 처리중')
                ->descriptionIcon('heroicon-o-arrow-path')
                ->color('warning'),
        ];
    }
}
