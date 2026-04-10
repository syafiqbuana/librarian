<?php

namespace App\Filament\Resources;

use App\Filament\Resources\UserResource\Pages;
use App\Filament\Resources\UserResource\RelationManagers;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Components\Section;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Grid;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Kredesnial Pengguna')

                    ->schema([
                        TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('email')
                            ->email()
                            ->unique(ignoreRecord: true)
                            ->required()
                            ->maxLength(255),
                        TextInput::make('password')
                            ->password()
                            ->required()
                            ->maxLength(255),
                        Select::make('role')
                            ->options([
                                'admin' => 'Admin',
                                'student' => 'Siswa',
                            ])
                            ->reactive()
                            ->required(),
                    ]),

                Section::make('Informasi Siswa')
                    ->hidden(fn($get) => $get('role') !== 'student')
                    ->relationship('studentDetail')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextInput::make('nis')
                                    ->label('NIS')
                                    ->maxLength(255)
                                    ->unique(ignoreRecord: true)
                                    ->required(),
                                Select::make('class')
                                    ->label('Kelas')
                                    ->options([
                                        'X' => 'Sepuluh',
                                        'XI' => 'Sebelas',
                                        'XII' => 'Dua Belas'
                                    ])
                                    ->required(),
                                Select::make('major')
                                    ->label('Jurusan')
                                    ->required()
                                    ->options([
                                        'RPL' => 'RPL',
                                        'TJKT' => 'TJKT',
                                        'AKL' =>'AKL',
                                        'TF' => 'TF',
                                        'MPLB' => 'MPLB',
                                        'PM' => 'PM'
                                    ]),
                                Select::make('gender')
                                    ->label('Jenis Kelamin')
                                    ->required()
                                    ->options([
                                        'Laki-laki' => 'Laki-laki',
                                        'Perempuan' => 'Perempuan'
                                    ]),
                                DatePicker::make('birth_date')
                                    ->label('Tanggal Lahir')
                                    ->required(),
                                TextInput::make('phone')
                                    ->numeric()
                                    ->maxLength(20),
                                TextArea::make('address')
                                    ->label('Alamat')
                                    ->required()
                                    ->columnSpanFull()
                            ])


                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name'),
                Tables\Columns\TextColumn::make('email'),
                Tables\Columns\TextColumn::make('role')->badge()->color(fn($state) => match ($state) {
                    'admin' => 'danger',
                    'student' => 'primary',
                    default => 'gray',
                })
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
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
