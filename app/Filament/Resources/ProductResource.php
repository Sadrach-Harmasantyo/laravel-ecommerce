<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProductResource\Pages;
use App\Filament\Resources\ProductResource\RelationManagers;
use App\Models\Product;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static ?string $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 4;

    protected static ?string $modelLabel = 'Produk';

    protected static ?string $pluralModelLabel = 'Produk';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Group::make()->schema([
                    Section::make('Product Information')->schema([

                        TextInput::make('name')
                            ->label('Nama')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(function (string $state, $operation, $set) {
                                if ($operation !== 'create') {
                                    return;
                                }
                                $set('slug', Str::slug($state));
                            }),

                        TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->disabled()
                            ->dehydrated()
                            ->unique(Product::class, 'slug', ignoreRecord: true),

                        MarkdownEditor::make('description')
                            ->columnSpanFull()
                            ->label('Deskripsi Panjang')
                            ->fileAttachmentsDirectory('products'),

                        MarkdownEditor::make('short_description')
                            ->columnSpanFull()
                            ->label('Deskripsi Pendek')
                            ->fileAttachmentsDirectory('products'),

                        TextInput::make('sku')
                            ->label('SKU')
                            ->required()
                            ->maxLength(255)
                            ->columnSpanFull(),
                    ])->columns(2),

                    Section::make('Varian Produk')->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Varian (e.g., Ukuran)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('value')
                                    ->label('Nilai Varian (e.g., XL, M, S)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('sku')
                                    ->label('SKU Varian')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->required()
                                    ->prefix('IDR'),

                                TextInput::make('stock_quantity')
                                    ->label('Jumlah Stok')
                                    ->numeric()
                                    ->required()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->required()
                                    ->default(true),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                    Section::make('Varian Produk')->schema([
                        Repeater::make('variants')
                            ->relationship()
                            ->schema([
                                TextInput::make('name')
                                    ->label('Nama Varian (e.g., Ukuran)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('value')
                                    ->label('Nilai Varian (e.g., XL, M, S)')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('sku')
                                    ->label('SKU Varian')
                                    ->required()
                                    ->maxLength(255),

                                TextInput::make('price')
                                    ->label('Harga')
                                    ->numeric()
                                    ->required()
                                    ->prefix('IDR'),

                                TextInput::make('stock_quantity')
                                    ->label('Jumlah Stok')
                                    ->numeric()
                                    ->required()
                                    ->default(0),

                                Toggle::make('is_active')
                                    ->label('Aktif')
                                    ->required()
                                    ->default(true),
                            ])
                            ->columns(3)
                            ->defaultItems(1)
                            ->reorderable()
                            ->collapsible()
                            ->columnSpanFull(),
                    ]),

                    Section::make('Gambar')->schema([
                        FileUpload::make('images')
                            ->label('Gambar')
                            ->multiple()
                            ->directory('products')
                            ->maxFiles(5)
                            ->reorderable()
                    ]),

                    Section::make('Data SEO')->schema([

                        TextInput::make('meta_title')
                            ->label('Judul Meta')
                            ->maxLength(255),

                        Textarea::make('meta_description')
                            ->label('Deskripsi Meta')
                            ->autosize(),

                        TextInput::make('meta_keywords')
                            ->label('Kata Kunci Meta')
                            ->maxLength(255),

                    ])
                ])->columnSpan(2),

                Group::make()->schema([

                    Section::make('Harga')->schema([
                        TextInput::make('price')
                            ->label('Harga')
                            ->numeric()
                            ->required()
                            ->helperText('Default price. Variants can have different prices.')
                            ->prefix('IDR')
                    ]),

                    Section::make('Hubungan')->schema([

                        Select::make('category_id')
                            ->label('Kategori')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('category', 'name'),

                        Select::make('brand_id')
                            ->label('Merek')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->relationship('brand', 'name'),
                    ]),

                    Section::make('Status')->schema([

                        Toggle::make('in_stock')
                            ->label('Stok')
                            ->required()
                            ->default(true)
                            ->helperText('Status produk utama. Cek stok varian untuk detail.'),

                        Toggle::make('is_active')
                            ->label('Aktif')
                            ->required()
                            ->default(true),

                        Toggle::make('is_featured')
                            ->label('Unggulan')
                            ->required(),

                        Toggle::make('on_sale')
                            ->label('Diskon')
                            ->required(),

                    ])

                ])->columnSpan(1)
            ])->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->label('Nama')
                    ->searchable(),

                Tables\Columns\TextColumn::make('category.name')
                    ->label('Kategori')
                    ->sortable(),

                Tables\Columns\TextColumn::make('brand.name')
                    ->label('Merek')
                    ->sortable(),

                Tables\Columns\TextColumn::make('price')
                    ->label('Harga')
                    ->money('IDR')
                    ->sortable(),

                Tables\Columns\TextColumn::make('variants_count')
                    ->counts('variants')
                    ->label('Varian'),

                Tables\Columns\IconColumn::make('is_active')
                    ->label('Aktif')
                    ->boolean(),

                Tables\Columns\IconColumn::make('is_featured')
                    ->label('Unggulan')
                    ->boolean(),

                Tables\Columns\IconColumn::make('in_stock')
                    ->label('Stok')
                    ->boolean(),

                Tables\Columns\IconColumn::make('on_sale')
                    ->label('Diskon')
                    ->boolean(),

                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category')
                    ->relationship('category', 'name'),

                SelectFilter::make('brand')
                    ->relationship('brand', 'name'),

                Filter::make('is_featured')
                    ->toggle(),

                Filter::make('in_stock')
                    ->toggle(),

                Filter::make('on_sale')
                    ->toggle(),

                Filter::make('is_active')
                    ->toggle()
            ])
            ->actions([
                Tables\Actions\ActionGroup::make([
                    Tables\Actions\ViewAction::make(),
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\DeleteAction::make()->requiresConfirmation(),
                ])
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->defaultSort('created_at', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            RelationManagers\VariantsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProducts::route('/'),
            'create' => Pages\CreateProduct::route('/create'),
            'edit' => Pages\EditProduct::route('/{record}/edit'),
        ];
    }
}
