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
            Stat::make('Total Pesanan', Order::count())
                ->description('Seluruh pesanan dalam sistem')
                ->descriptionIcon('heroicon-m-shopping-cart')
                ->color('primary'),
                
            Stat::make('Pesanan Tertunda', Order::where('status', 'new')->count())
                ->description('Pesanan menunggu untuk diproses')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
                
            Stat::make('Pesanan Selesai', Order::whereIn('status', ['delivered', 'completed'])->count())
                ->description('Pesanan berhasil dikirimkan')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
                
            Stat::make('Total Pendapatan', 'IDR ' . number_format(Order::where('payment_status', 'paid')->sum('grand_total'), 2))
                ->description('Dari pesanan yang dibayar')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),
        ];
    }
}
