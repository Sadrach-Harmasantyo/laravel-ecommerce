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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                FormsSection::make('Order Information')->schema([
                    Select::make('user_id')
                        ->label('Customer')
                        ->relationship('user', 'name')
                        ->searchable()
                        ->preload()
                        ->required(),
                        
                    TextInput::make('order_number')
                        ->label('Order Number')
                        ->default('ORD-' . time() . '-' . rand(1000, 9999))
                        ->required(),
                        
                    Select::make('status')
                        ->label('Order Status')
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
                        ->label('Payment Method')
                        ->options(function () {
                            // Get all active banks and format them for the dropdown
                            return \App\Models\Bank::where('is_active', true)
                                ->get()
                                ->pluck('name', 'name')
                                ->toArray();
                        })
                        ->required(),
                        
                    Select::make('payment_status')
                        ->label('Payment Status')
                        ->options([
                            'pending' => 'Pending',
                            'paid' => 'Paid',
                            'failed' => 'Failed',
                            'refunded' => 'Refunded',
                        ])
                        ->default('pending')
                        ->required(),
                        
                    TextInput::make('grand_total')
                        ->label('Grand Total')
                        ->numeric()
                        ->required(),
                        
                    TextInput::make('shipping_amount')
                        ->label('Shipping Amount')
                        ->numeric()
                        ->default(0)
                        ->required(),
                        
                    Select::make('shipping_method')
                        ->label('Shipping Method')
                        ->options([
                            'standard' => 'Standard Shipping',
                            'express' => 'Express Shipping',
                            'free' => 'Free Shipping',
                        ])
                        ->default('standard')
                        ->required(),

                    TextInput::make('tracking_number')
                        ->label('Tracking Number')
                        ->helperText('Enter tracking number when order is shipped')
                        ->visible(function (callable $get) {
                            return $get('status') === 'shipped';
                        }),
                        
                    Select::make('currency')
                        ->label('Currency')
                        ->options([
                            'IDR' => 'Indonesian Rupiah (Rp)',
                        ])
                        ->default('IDR')
                        ->disabled() // Make it disabled since we only use IDR
                        ->dehydrated() // Still save the value to database
                        ->required(),
                        
                    Textarea::make('notes')
                        ->label('Order Notes')
                        ->rows(3),
                        
                    FileUpload::make('payment_proof')
                        ->label('Payment Proof')
                        ->directory('payment_proofs')
                        ->visibility('public')
                        ->image(),
                ]),
                
                // Change this:
                // Section::make('Shipping Address')->schema([
                // To this:
                FormsSection::make('Shipping Address')->schema([
                    Forms\Components\Repeater::make('address')
                        ->relationship()
                        ->schema([
                            TextInput::make('first_name')
                                ->required(),
                            TextInput::make('last_name')
                                ->required(),
                            TextInput::make('phone')
                                ->required(),
                            Textarea::make('address')
                                ->required(),
                            TextInput::make('city')
                                ->required(),
                            TextInput::make('state')
                                ->required(),
                            TextInput::make('zip')
                                ->required(),
                            Select::make('type')
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
                FormsSection::make('Order Items')->schema([
                    Forms\Components\Repeater::make('items')
                        ->relationship()
                        ->schema([
                            Select::make('product_id')
                                ->relationship('product', 'name')
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('quantity')
                                ->numeric()
                                ->default(1)
                                ->required(),
                            TextInput::make('unit_amount')
                                ->numeric()
                                ->required(),
                            TextInput::make('total_amount')
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
                    ->label('Grand Total')
                    ->money('IDR')
                    ->sortable(),

                TextColumn::make('id')
                    ->label('Order ID')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('user.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('status')
                    ->label('Order Status')
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
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('tracking_number')
                    ->label('Tracking Number')
                    ->searchable()
                    ->copyable()
                    ->url(fn ($record) => $record->tracking_number ? "https://cekresi.com/?noresi={$record->tracking_number}" : null, true)
                    ->icon('heroicon-m-truck')
                    ->visible(fn ($record) => !empty($record->tracking_number)),

                TextColumn::make('payment_method')
                    ->label('Payment Method')
                    ->formatStateUsing(function (string $state) {
                        // Simply return the bank name as is
                        return $state;
                    })
                    ->icon('heroicon-m-credit-card')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('payment_status')
                    ->label('Payment Status')
                    ->sortable()
                    ->badge()
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->label('Order Date')
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
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\Action::make('checkTracking')
                    ->label('Check Tracking')
                    ->icon('heroicon-o-map')
                    ->color('success')
                    ->url(fn ($record) => $record->tracking_number ? "https://cekresi.com/?noresi={$record->tracking_number}" : null, true)
                    ->visible(fn ($record) => !empty($record->tracking_number) && $record->status === 'shipped')
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

    // Remove the infolist method completely

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

    public static function getNavigationBadge() : ?string {
        return static::getModel()::count();
    }

}


