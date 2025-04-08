<?php

namespace App\Filament\Resources;

use App\Filament\Resources\OrderResource\Pages;
use App\Filament\Resources\OrderResource\RelationManagers;
use App\Filament\Resources\OrderResource\Widgets\OrderStats;
use App\Models\Order;
use Filament\Forms;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Section as FormsSection;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\Section as InfolistsSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Notifications\Notification;

class OrderResource extends Resource
{
    protected static ?string $model = Order::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 5;

    protected static ?string $modelLabel = 'Pesanan';

    protected static ?string $pluralModelLabel = 'Pesanan';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormsSection::make('Order Information')->schema([
                    Select::make('user_id')
                        ->label('Pelanggan')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),

                    TextInput::make('order_number')
                        ->label('Nomor Pesanan')
                        ->default('ORD-' . time() . '-' . rand(1000, 9999))
                        ->required(),

                    Select::make('status')
                        ->label('Status Pesanan')
                        ->options([
                            'new' => 'New',
                            'processing' => 'Processing',
                            'shipped' => 'Shipped',
                            'delivered' => 'Delivered',
                            'cancelled' => 'Cancelled',
                        ])
                        ->default('new')
                        ->required(),

                    Select::make('payment_method')
                        ->label('Metode Pembayaran')
                        ->options(function () {
                            // Get all active banks and format them for the dropdown
                            return \App\Models\Bank::where('is_active', true)
                                ->get()
                                ->pluck('name', 'name')
                                ->toArray();
                        })
                        ->required(),

                    Select::make('payment_status')
                        ->label('Status Pembayaran')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                            'refunded' => 'Refunded',
                        ])
                        ->default('pending')
                        ->required(),

                    TextInput::make('grand_total')
                        ->label('Total Keseluruhan')
                        ->numeric()
                        ->required(),

                    TextInput::make('shipping_amount')
                        ->label('Pengiriman')
                        ->numeric()
                        ->default(0)
                        ->required(),

                    Select::make('shipping_method')
                        ->label('Metode Pengiriman')
                        ->options([
                            'standard' => 'Standard Shipping',
                            'express' => 'Express Shipping',
                            'free' => 'Free Shipping',
                        ])
                        ->default('standard')
                        ->required(),

                    TextInput::make('tracking_number')
                        ->label('Nomor Resi')
                        ->helperText('Enter tracking number when order is shipped')
                        ->visible(function (callable $get) {
                            return $get('status') === 'shipped';
                        }),

                    Select::make('currency')
                        ->label('Mata Uang')
                        ->options([
                            'IDR' => 'Indonesian Rupiah (Rp)',
                        ])
                        ->default('IDR')
                        ->disabled() // Make it disabled since we only use IDR
                        ->dehydrated() // Still save the value to database
                        ->required(),

                    Textarea::make('notes')
                        ->label('Catatan Pesanan')
                        ->rows(3),

                    FileUpload::make('payment_proof')
                        ->label('Bukti Pembayaran')
                        ->directory('payment_proofs')
                        ->visibility('public')
                        ->image(),
                ]),

                // Change this:
                // Section::make('Shipping Address')->schema([
                // To this:
                FormsSection::make('Alamat Pengiriman')->schema([
                    Forms\Components\Repeater::make('address')
                        ->label('Alamat')
                        ->relationship()
                        ->schema([
                            TextInput::make('first_name')
                                ->label('Nama Depan')
                                ->required(),
                            TextInput::make('last_name')
                                ->label('Nama Belakang')
                                ->required(),
                            TextInput::make('phone')
                                ->label('Nomor Telepon')
                                ->required(),
                            Textarea::make('address')
                                ->label('Alamat')
                                ->required(),
                            TextInput::make('city')
                                ->label('Kota')
                                ->required(),
                            TextInput::make('state')
                                ->label('Provinsi')
                                ->required(),
                            TextInput::make('zip')
                                ->label('Kode Pos')
                                ->required(),
                            Select::make('type')
                                ->label('Tipe')
                                ->options([
                                    'shipping' => 'Shipping',
                                    'billing' => 'Billing',
                                ])
                                ->default('shipping')
                                ->required(),
                        ])
                        ->maxItems(1),
                ]),

                // Change this:
                // Section::make('Order Items')->schema([
                // To this:
                FormsSection::make('Barang Pesanan')->schema([
                    Forms\Components\Repeater::make('items')
                        ->label('Barang')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->label('Produk')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('variant_name')
                                ->label('Nama Varian')
                                ->maxLength(255),
                            TextInput::make('variant_value')
                                ->label('Nilai Varian')
                                ->maxLength(255),
                            TextInput::make('sku')
                                ->label('SKU')
                                ->maxLength(255),
                            TextInput::make('quantity')
                                ->label('Jumlah')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            TextInput::make('unit_amount')
                                ->label('Harga Satuan')
                                ->numeric()
                                ->required(),
                            TextInput::make('total_amount')
                                ->label('Total')
                                ->numeric()
                                ->required(),
                        ]),
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('grand_total')
                    ->label('Total Keseluruhan')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('id')
                    ->label('ID Pesanan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Pelanggan')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Status Pesanan')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'new' => 'info',
                        'processing' => 'warning',
                        'shipped' => 'success',
                        'delivered' => 'success',
                        'cancelled' => 'danger',
                    })
                    ->icon(fn(string $state): string => match ($state) {
                        'new' => 'heroicon-m-sparkles',
                        'processing' => 'heroicon-m-arrow-path',
                        'shipped' => 'heroicon-m-truck',
                        'delivered' => 'heroicon-m-check-badge',
                        'cancelled' => 'heroicon-m-x-circle',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'new' => 'Baru',
                        'processing' => 'Diproses',
                        'shipped' => 'Dikirim',
                        'delivered' => 'Terkirim',
                        'cancelled' => 'Dibatalkan',
                        default => $state,
                    })
                    ->sortable()
                    ->searchable(),

                TextColumn::make('tracking_number')
                    ->label('Nomor Resi')
                    ->searchable()
                    ->copyable()
                    ->url(fn($record) => $record->tracking_number ? "https://cekresi.com/?noresi={$record->tracking_number}" : null, true)
                    ->icon('heroicon-m-truck')
                    ->visible(fn($record) => !empty($record->tracking_number)),

                TextColumn::make('payment_method')
                    ->label('Metode Pembayaran')
                    ->formatStateUsing(function (string $state) {
                        // Simply return the bank name as is
                        return $state;
                    })
                    ->icon('heroicon-m-credit-card')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Status Pembayaran')
                    ->color(fn(string $state): string => match ($state) {
                        'pending' => 'warning',
                        'paid' => 'success',
                        'failed' => 'danger',
                        'refunded' => 'info',
                    })
                    ->formatStateUsing(fn(string $state): string => match ($state) {
                        'pending' => 'Tertunda',
                        'paid' => 'Dibayar',
                        'failed' => 'Gagal',
                        'refunded' => 'Dikembalikan',
                        default => $state,
                    })
                    ->sortable()
                    ->badge()
                    ->searchable(),

                TextColumn::make('created_at')
                    ->label('Tanggal Dibuat')
                    ->dateTime()
                    ->sortable()
                    ->searchable(),
            ])
            ->filters([
                Tables\Filters\SelectFilter::make('status')
                    ->options([
                        'new' => 'New',
                        'processing' => 'Processing',
                        'shipped' => 'Shipped',
                        'delivered' => 'Delivered',
                        'cancelled' => 'Cancelled',
                    ]),
                Tables\Filters\SelectFilter::make('payment_status')
                    ->options([
                        'pending' => 'Pending',
                        'paid' => 'Paid',
                        'failed' => 'Failed',
                        'refunded' => 'Refunded',
                    ]),
                Tables\Filters\SelectFilter::make('user_id')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Customer'),
            ])
            ->actions([
                Tables\Actions\ViewAction::make()
                    ->label('Lihat'),
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
                Tables\Actions\Action::make('checkTracking')
                    ->label('Cek Resi')
                    ->icon('heroicon-o-map')
                    ->color('success')
                    ->url(fn($record) => $record->tracking_number ? "https://cekresi.com/?noresi={$record->tracking_number}" : null, true)
                    ->visible(fn($record) => !empty($record->tracking_number) && $record->status === 'shipped')
                    ->action(function (Order $record, array $data): void {
                        $record->update([
                            'tracking_number' => $data['tracking_number'],
                            'status' => 'shipped', // Automatically set status to shipped
                        ]);

                        Notification::make()
                            ->title('Tracking number updated')
                            ->success()
                            ->send();
                    })
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                InfolistsSection::make('Informasi Pesanan')
                    ->schema([
                        TextEntry::make('user.name')
                            ->label('Pelanggan'),
                        TextEntry::make('order_number')
                            ->label('Nomor Pesanan'),
                        TextEntry::make('status')
                            ->label('Status Pesanan')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'new' => 'info',
                                'processing' => 'warning',
                                'shipped' => 'success',
                                'delivered' => 'success',
                                'cancelled' => 'danger',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'new' => 'Baru',
                                'processing' => 'Diproses',
                                'shipped' => 'Dikirim',
                                'delivered' => 'Terkirim',
                                'cancelled' => 'Dibatalkan',
                                default => $state,
                            }),
                        TextEntry::make('payment_method')
                            ->label('Metode Pembayaran'),
                        TextEntry::make('payment_status')
                            ->label('Status Pembayaran')
                            ->badge()
                            ->color(fn(string $state): string => match ($state) {
                                'pending' => 'warning',
                                'paid' => 'success',
                                'failed' => 'danger',
                                'refunded' => 'info',
                            })
                            ->formatStateUsing(fn(string $state): string => match ($state) {
                                'pending' => 'Tertunda',
                                'paid' => 'Dibayar',
                                'failed' => 'Gagal',
                                'refunded' => 'Dikembalikan',
                                default => $state,
                            }),
                        TextEntry::make('grand_total')
                            ->label('Total Keseluruhan')
                            ->money('IDR'),
                        TextEntry::make('shipping_amount')
                            ->label('Biaya Pengiriman')
                            ->money('IDR'),
                        TextEntry::make('shipping_method')
                            ->label('Metode Pengiriman'),
                        TextEntry::make('tracking_number')
                            ->label('Nomor Resi')
                            ->url(fn($record) => $record->tracking_number ? "https://cekresi.com/?noresi={$record->tracking_number}" : null, true)
                            ->visible(fn($record) => !empty($record->tracking_number)),
                        TextEntry::make('currency')
                            ->label('Mata Uang'),
                        TextEntry::make('notes')
                            ->label('Catatan Pesanan')
                            ->markdown(),
                        ImageEntry::make('payment_proof')
                            ->label('Bukti Pembayaran'),
                    ]),

                InfolistsSection::make('Barang Pesanan')
                    ->schema([
                        Infolists\Components\RepeatableEntry::make('items')
                            ->label('Barang')
                            ->schema([
                                TextEntry::make('product.name')
                                    ->label('Produk'),
                                TextEntry::make('variant_name')
                                    ->label('Nama Varian'),
                                TextEntry::make('variant_value')
                                    ->label('Nilai Varian'),
                                TextEntry::make('sku')
                                    ->label('SKU'),
                                TextEntry::make('quantity')
                                    ->label('Jumlah'),
                                TextEntry::make('unit_amount')
                                    ->label('Harga Satuan')
                                    ->money('IDR'),
                                TextEntry::make('total_amount')
                                    ->label('Total')
                                    ->money('IDR'),
                            ])
                            ->columns(4),
                    ]),

                // Fix: Change RepeatableEntry to just displaying a single address
                InfolistsSection::make('Alamat Pengiriman')
                    ->schema([
                        // For hasOne relationship, use TextEntry directly with dot notation
                        TextEntry::make('address.first_name')
                            ->label('Nama Depan'),
                        TextEntry::make('address.last_name')
                            ->label('Nama Belakang'),
                        TextEntry::make('address.phone')
                            ->label('Nomor Telepon'),
                        TextEntry::make('address.address')
                            ->label('Alamat'),
                        TextEntry::make('address.city')
                            ->label('Kota'),
                        TextEntry::make('address.state')
                            ->label('Provinsi'),
                        TextEntry::make('address.zip')
                            ->label('Kode Pos'),
                        TextEntry::make('address.type')
                            ->label('Tipe'),
                    ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\ItemsRelationManager::class,
            RelationManagers\AddressRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListOrders::route('/'),
            'create' => Pages\CreateOrder::route('/create'),
            'view' => Pages\ViewOrder::route('/{record}'),
            'edit' => Pages\EditOrder::route('/{record}/edit'),
        ];
    }

    public static function getWidgets(): array
    {
        return [
            OrderStats::class,
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }
}
