<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BlogResource\Pages;
use App\Models\Blog;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BlogResource extends Resource
{
    protected static ?string $model = Blog::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Artikel';

    // Tambahkan properti berikut untuk mengubah label model
    protected static ?string $modelLabel = 'Artikel';
    
    // Tambahkan properti berikut untuk mengubah label plural model (header)
    protected static ?string $pluralModelLabel = 'Artikel';

    // protected static ?string $navigationGroup = 'Content';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Detail Artikel')
                    ->schema([
                        Forms\Components\TextInput::make('title')
                            ->label('Judul')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn (string $operation, $state, Forms\Set $set) => 
                                $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        
                        Forms\Components\TextInput::make('slug')
                            ->required()
                            ->maxLength(255)
                            ->unique(Blog::class, 'slug', ignoreRecord: true),
                        
                        Forms\Components\FileUpload::make('featured_image')
                            ->label('Gambar Unggulan')
                            ->image()
                            ->directory('blog-images')
                            ->visibility('public')
                            ->imageEditor(),
                        
                        Forms\Components\Textarea::make('excerpt')
                            ->label('Ringkasan')
                            ->rows(3)
                            ->maxLength(500)
                            ->helperText('Ringkasan pendek dari artikel'),
                        
                        Forms\Components\RichEditor::make('content')
                            ->label('Konten')
                            ->required()
                            ->columnSpanFull(),
                        
                        Forms\Components\Toggle::make('is_published')
                            ->label('Tampilkan di halaman depan')
                            ->default(false)
                            ->helperText('Artikel akan ditampilkan di halaman depan jika diaktifkan'),
                        
                        Forms\Components\DateTimePicker::make('published_at')
                            ->label('Tanggal Pembuatan')
                            ->default(now())
                            ->visible(fn (Forms\Get $get) => $get('is_published')),

                        Forms\Components\Hidden::make('user_id')
                            ->default(fn () => auth()->id()),
                    ])->columns(2),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\ImageColumn::make('featured_image')
                    ->label('Gambar'),
                
                Tables\Columns\TextColumn::make('title')
                    ->label('Judul')
                    ->searchable()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Penulis')
                    ->sortable(),
                
                Tables\Columns\IconColumn::make('is_published')
                    ->label('Publik')
                    ->boolean()
                    ->sortable(),
                
                Tables\Columns\TextColumn::make('published_at')
                    ->label('Tanggal Pembuatan')
                    ->dateTime()
                    ->sortable(),
                
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
                Tables\Filters\SelectFilter::make('is_published')
                    ->options([
                        '1' => 'Published',
                        '0' => 'Draft',
                    ])
                    ->label('Status'),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->label('Edit'),
                Tables\Actions\DeleteAction::make()
                    ->label('Hapus'),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBlogs::route('/'),
            'create' => Pages\CreateBlog::route('/create'),
            'edit' => Pages\EditBlog::route('/{record}/edit'),
        ];
    }
}
