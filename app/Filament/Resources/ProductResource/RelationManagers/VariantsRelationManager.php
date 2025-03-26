<?php

namespace App\Filament\Resources\ProductResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;

class VariantsRelationManager extends RelationManager
{
    protected static string $relationship = 'variants';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Variant Name (e.g., Size)')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('value')
                    ->label('Variant Value (e.g., XL, M, S)')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('sku')
                    ->label('Variant SKU')
                    ->required()
                    ->maxLength(255),
                    
                Forms\Components\TextInput::make('price')
                    ->label('Price')
                    ->numeric()
                    ->required()
                    ->prefix('IDR'),
                    
                Forms\Components\TextInput::make('stock_quantity')
                    ->label('Stock Quantity')
                    ->numeric()
                    ->required()
                    ->default(0),
                    
                Forms\Components\Toggle::make('is_active')
                    ->label('Active')
                    ->required()
                    ->default(true),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('name')
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Variant Type'),
                    
                Tables\Columns\TextColumn::make('value')
                    ->label('Value'),
                    
                Tables\Columns\TextColumn::make('sku'),
                
                Tables\Columns\TextColumn::make('price')
                    ->money('IDR')
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('stock_quantity')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('is_active')
                    ->boolean(),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make()->requiresConfirmation()
                    ->after(function ($record) {
                        // Update parent product stock status after deleting a variant
                        $record->product->updateStockStatus();
                    }),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make()
                        ->after(function ($records) {
                            // Get the product to update after bulk action
                            if ($records->isNotEmpty()) {
                                $product = $records->first()->product;
                                $product->updateStockStatus();
                            }
                        }),
                ]),
            ]);
    }
}