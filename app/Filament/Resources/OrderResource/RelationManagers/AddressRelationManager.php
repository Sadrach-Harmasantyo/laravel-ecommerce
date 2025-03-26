<?php

namespace App\Filament\Resources\OrderResource\RelationManagers;

use Filament\Forms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class AddressRelationManager extends RelationManager
{
    protected static string $relationship = 'address';

    protected static ?string $title = 'Alamat';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('first_name')
                    ->label('Nama Depan')
                    ->required()
                    ->maxLength(255),
                
                TextInput::make('last_name')
                    ->label('Nama Belakang')
                    ->required()
                    ->maxLength(255),

                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->required()
                    ->tel()
                    ->maxLength(20),

                Textarea::make('address')
                    ->label('Alamat')
                    ->required()
                    ->columnSpanFull(),

                TextInput::make('city')
                    ->label('Kota')
                    ->required()
                    ->maxLength(255),

                TextInput::make('state')
                    ->label('Provinsi')
                    ->required()
                    ->maxLength(255),

                TextInput::make('zip')
                    ->label('Kode Pos')
                    ->required()
                    ->maxLength(10),
            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('address')
            ->columns([
                TextColumn::make('full_name')
                    ->label('Nama Lengkap')
                    ->getStateUsing(function ($record) {
                        return $record->first_name . ' ' . $record->last_name;
                    }),

                TextColumn::make('phone')
                    ->label('Nomor Telepon'),

                TextColumn::make('city')
                    ->label('Kota'),

                TextColumn::make('state')
                    ->label('Provinsi'),

                TextColumn::make('zip')
                    ->label('Kode Pos'),

                TextColumn::make('address')
                    ->label('Alamat')
                    ->limit(30),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
