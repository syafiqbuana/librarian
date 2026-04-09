<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowingResource\Pages;
use App\Filament\Resources\BorrowingResource\RelationManagers;
use App\Models\Visitor;
use App\Models\Borrowing;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use GrahamCampbell\ResultType\Success;
use Carbon\Carbon;
use App\Models\Book;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Tables\Actions\Action;
use Illuminate\Support\Facades\DB;
use Filament\Notifications\Notification;
use App\Models\User;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Grid;
class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Peminjam')->schema([
                    Grid::make(2)->schema([

                        Select::make('user_id')
                            ->label('Peminjam')
                            ->options(User::where('role', 'student')->pluck('name', 'id'))
                            ->searchable()
                            ->placeholder('Pilih peminjam')
                            ->required()
                            ->live()
                            ->disabled(fn($get) => filled($get('visitor_id')))
                            ->helperText('Pilih peminjam dari daftar pengguna yang terdaftar'),

                        Select::make('visitor_id')
                            ->label('Pengunjung')
                            ->disabled(fn($get) => filled($get('user_id')))
                            ->relationship('visitor', 'name')
                            ->options(Visitor::pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->placeholder('Pilih pengunjung')
                            ->createOptionForm([
                                TextInput::make('name')->required()->maxLength(255),
                                TextInput::make('email')->email()->required()->maxLength(255),
                                TextInput::make('phone_number')->tel()->required()->maxLength(20),
                            ]),
                        DatePicker::make('borrow_date')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('due_date', Carbon::parse($state)->addDays(14)->format('Y-m-d'));
                                }
                            }),
                        DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required()
                    ]),
                    Section::make('Buku Yang Dipinjam')
                        ->schema([
                            Repeater::make('book_list')
                                ->label('List Buku')
                                ->maxItems(3)
                                ->relationship('borrowingDetail')
                                ->schema([
                                    Select::make('book_id')
                                        ->label('Buku')
                                        ->relationship('book', 'title')
                                        ->options(Book::where('stock', '>', 0)->pluck('title', 'id'))
                                        ->searchable()
                                        ->required(),
                                    TextInput::make('quantity')
                                        ->label('Jumlah')
                                        ->numeric()
                                        ->required()
                                        ->default(1)
                                        ->minValue(1)
                                        ->maxValue(1)
                                ]),
                        ])
                ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('user.name')->label('Peminjam')->searchable(),
                TextColumn::make('borrow_date')->date()->label('Tanggal Pinjam')->sortable(),
                TextColumn::make('due_date')->date()->label('Tanggal Jatuh Tempo')->sortable(),
                TextColumn::make('status')->label('Status')->badge()->color(
                    fn($state) => match ($state) {
                        'borrowed' => 'primary',
                        'waiting' => 'info',
                        'returned_late' => 'danger',
                        'pending_return' => 'info',
                        'returned' => 'success'
                    }
                )->formatStateUsing(
                        fn($state) => match ($state) {
                            'borrowed' => 'Dipinjam',
                            'returned_late' => 'Dikembalikan Terlambat',
                            'pending_return' => 'Menunggu Konfirmasi',
                            'waiting' => 'Menunggu Dikonformasi',
                            'returned' => 'Dikembalikan',
                            default => $state
                        }
                    ),
                TextColumn::make('return_date')
                    ->label('Tanggal Dikembalikan')
                    ->date()
                    ->color(fn($state) => $state === null ? 'danger' : 'success')
                    ->badge()
                    ->placeholder('Belum dikembalikan'),
                TextColumn::make('fine')
                    ->label('Denda')
                    ->money('idr')
                    ->color(fn($state) => $state === null ? 'danger' : 'success')
            ])
            ->filters([
                //
            ])
            ->actions([
                //view untuk melihat detail peminjaman
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->url(fn($record) => static::getUrl('view', ['record' => $record]))
                    ->color('info'),
                Action::make('approve_borrow')->label('Setujui Peminjaman')->visible(fn($record) => $record->isWaiting())->action(function ($record) {
                    $record->update(['status' => 'borrowed']);
                    $record->save();
                })->color('success'),
                Action::make('confirm_return')
                    ->label('Konfirmasi Pengembalian')
                    ->color('success')
                    ->requiresConfirmation()
                    ->visible(fn($record) => $record->isPendingReturn())
                    ->action(function ($record) {

                        DB::transaction(function () use ($record) {
                            $record->update([
                                'return_date' => now(),
                                'status' => $record->isOverdue() ? 'returned_late' : 'returned'
                            ]);

                            foreach ($record->borrowingDetail as $detail) {
                                $detail->book->increment('stock', $detail->quantity);
                            }

                            Notification::make()
                                ->title('Pengembalian Dikonfirmasi')
                                ->success()
                                ->color('info')
                                ->send();
                        });
                    })
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
            'index' => Pages\ListBorrowings::route('/'),
            'create' => Pages\CreateBorrowing::route('/create'),
            'view' => Pages\ViewBorrowing::route('/{record}'),
            'edit' => Pages\EditBorrowing::route('/{record}/edit'),
        ];
    }
}
