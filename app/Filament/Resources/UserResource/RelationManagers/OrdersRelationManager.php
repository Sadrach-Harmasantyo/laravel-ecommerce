<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Filament\Resources\OrderResource;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class OrdersRelationManager extends RelationManager
{
    protected static string $relationship = 'orders';

    protected static ?string $title = 'Pesanan';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                //
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('id')
            ->columns([

                TextColumn::make('grand_total')
                    ->label('Total Pembayaran')
                    ->money('IDR')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->color(fn (string $state): string => match($state){
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn (string $state): string => match($state){
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state){
                        'new' => 'Baru',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Terkirim',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->formatStateUsing(fn (string $state): string => match($state) {
                        'bank_transfer' => 'Bank Transfer',
                        'cash_on_delivery' => 'Cash on Delivery',
                        'credit_card' => 'Credit Card',
                        'paypal' => 'PayPal',
                        'bca' => 'BCA',
                        'bni' => 'BNI',
                        'mandiri' => 'Mandiri',
                        default => ucfirst(str_replace('_', ' ', $state)),
                    })
                    ->icon('heroicon-m-credit-card')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->sortable()
                    ->badge()
                    ->color(fn (string $state): string => match($state){
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn (string $state): string => match($state){
                        'pending' => 'Tertunda',
                        'paid' => 'Dibayar',
                        'failed' => 'Gagal',
                        'refunded' => 'Dikembalikan',
                        default => $state,
                    })
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->label('Tanggal Pesanan')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                // Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Action::make('Lihat')
                    ->url(fn (Order $record):string => OrderResource::getUrl('view', ['record' => $record]))
                    ->color('info')
                    ->icon('heroicon-o-eye'),
                    
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
