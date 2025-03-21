<?php

namespace App\Filament\Pages;

use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Filament\Widgets\LatestOrders;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected function getHeaderWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    protected function getFooterWidgets(): array
    {
        return [
            LatestOrders::class,
        ];
    }
}