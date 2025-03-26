<?php

namespace App\Filament\Widgets;

use App\Models\Order;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestOrders extends BaseWidget
{
    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = 2;
    
    // Mengubah judul widget
    protected static ?string $heading = 'Pesanan Terbaru';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Order::query()->latest()->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('order_number')
                    ->searchable()
                    ->sortable()
                    ->label('Nomor Pesanan'),
                Tables\Columns\TextColumn::make('user.name')
                    ->searchable()
                    ->sortable()
                    ->label('Pelanggan'),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Tanggal Pesanan'),
                Tables\Columns\TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->color(fn (string $state): string => match($state){
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state){
                        'new' => 'Baru',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Terkirim',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->icon(fn (string $state): string => match($state){
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                    })
                    ->sortable()
                    ->label('Status'),
                Tables\Columns\TextColumn::make('payment_status')
                    ->badge()
                    ->color(fn (string $state): string => match($state){
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state){
                        'pending' => 'Tertunda',
                        'paid' => 'Dibayar',
                        'failed' => 'Gagal',
                        'refunded' => 'Dikembalikan',
                        default => $state,
                    })
                    ->sortable()
                    ->label('Status Pembayaran'),
                Tables\Columns\TextColumn::make('grand_total')
                    ->money('IDR')
                    ->sortable()
                    ->label('Total'),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->url(fn (Order $record): string => route('filament.admin.resources.orders.view', $record))
                    ->icon('heroicon-m-eye')
                    ->label('Lihat'),
            ]);
    }
}
