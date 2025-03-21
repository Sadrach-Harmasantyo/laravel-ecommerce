<?php

namespace App\Filament\Resources\OrderResource\Widgets;

use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class OrderStats extends BaseWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make('Total Orders', Order::count())
                ->description('All orders in the system')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
                
            Stat::make('Pending Orders', Order::where('status', 'new')->count())
                ->description('Orders waiting to be processed')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Completed Orders', Order::whereIn('status', ['delivered', 'completed'])->count())
                ->description('Successfully delivered orders')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Total Revenue', 'IDR ' . number_format(Order::where('payment_status', 'paid')->sum('grand_total'), 2))
                ->description('From paid orders')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
