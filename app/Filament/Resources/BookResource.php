<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BookResource\Pages;
use App\Filament\Resources\BookResource\RelationManagers;
use App\Models\Book;
use Filament\Forms;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use File;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BookResource extends Resource
{
    protected static ?string $model = Book::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')
                    ->required(),
                Select::make('genres')
                    ->relationship('genres', 'name')
                    ->multiple()
                    ->preload()
                    ->required()
                    ,
                TextInput::make('author')
                    ->required(),
                TextInput::make('publisher')
                    ->required(),
                TextInput::make('publication_year')
                    ->required()
                    ->numeric()
                    ->minValue(1000)
                    ->maxValue(date('Y')),
                TextInput::make('isbn')
                    ->required()
                    ->unique(ignoreRecord: true),
                TextInput::make('stock')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                FileUpload::make('cover_image')
                    ->required()
                    ->image()
                    ->directory('book-covers')
                    ->disk('public')
                    ->preserveFilenames(),
                TextInput::make('description')
                    ->nullable(),
                TextInput::make('rack_location')
                    ->required(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('book_code'),
                Tables\Columns\TextColumn::make('title')->searchable(),
                Tables\Columns\TextColumn::make('author')->searchable(),
                Tables\Columns\TextColumn::make('publisher')->searchable(),
                Tables\Columns\TextColumn::make('publication_year'),
                Tables\Columns\TextColumn::make('isbn'),
                Tables\Columns\ImageColumn::make('cover_image')
                    ->disk('public'),
                Tables\Columns\TextColumn::make('stock'),
                Tables\Columns\TextColumn::make('rack_location'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListBooks::route('/'),
            'create' => Pages\CreateBook::route('/create'),
            'edit' => Pages\EditBook::route('/{record}/edit'),
        ];
    }
}
