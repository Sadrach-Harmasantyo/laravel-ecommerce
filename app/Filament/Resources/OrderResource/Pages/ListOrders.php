<?php

namespace App\Filament\Resources\OrderResource\Pages;

use App\Filament\Resources\OrderResource;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Tambah Pesanan'),
        ];
    }

    protected function getHeaderWidgets(): array {
        return [
            OrderStats::class,
        ];
    }
    
    public function getTabs(): array {
        return [
            null => Tab::make('Semua Pesanan'),
            'Baru' => Tab::make()->query(fn ($query) => $query->where('status', 'new')),
            'Diproses' => Tab::make()->query(fn ($query) => $query->where('status', 'processing')),
            'Dikirim' => Tab::make()->query(fn ($query) => $query->where('status', 'shipped')),
            'Terkirim' => Tab::make()->query(fn ($query) => $query->where('status', 'delivered')),
            'Dibatalkan' => Tab::make()->query(fn ($query) => $query->where('status', 'cancelled')),
        ];
    }
}