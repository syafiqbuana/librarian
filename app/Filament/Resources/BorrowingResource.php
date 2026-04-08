<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowingResource\Pages;
use App\Filament\Resources\BorrowingResource\RelationManagers;
use App\Models\Borrowing;
use Filament\Forms;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use GrahamCampbell\ResultType\Success;
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
use Carbon\Carbon;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Hidden;
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
                            ->label('Akun Peminjam')
                            ->options(User::where('role', 'student')->pluck('name', 'id'))
                            ->searchable()
                            ->live()
                            ->placeholder('Pilih peminjam')
                            ->afterStateUpdated(function ($state, callable $set, callable $get) {
                                if ($state) {
                                    // Jika user dipilih, isi name otomatis dari data user
                                    $user = \App\Models\User::find($state);
                                    $set('name', $user?->name);
                                } else {
                                    // Jika user dikosongkan, kosongkan name biar admin isi manual
                                    $set('name', null);
                                }
                            })

                            ->helperText('Pilih peminjam dari daftar pengguna yang terdaftar'),

                        TextInput::make('name')
                            ->label('Nama Peminjam')
                            ->required()
                            ->disabled(fn(callable $get) => filled($get('user_id')))
                            // disabled jika user_id sudah dipilih, aktif jika kosong
                            ->dehydrated(true) // pastikan tetap tersimpan walau disabled
                            ->placeholder('Masukkan nama peminjam')
                            ->helperText('Masukkan nama peminjam secara manual jika tidak ditemukan dalam daftar pengguna'),
                        DatePicker::make('borrow_date')
                            ->label('Tanggal Pinjam')
                            ->required()
                            ->live()
                            ->afterStateUpdated(function ($state, callable $set) {
                                if ($state) {
                                    $set('due_date', Carbon::parse($state)->addDays(14)->format('Y-m-d'));
                                }
                            })
                            ->helperText('Pilih tanggal peminjaman'),
                        DatePicker::make('due_date')
                            ->label('Tanggal Jatuh Tempo')
                            ->required()
                            ->helperText('atuh tempo 14 hari setelah tanggal pinjam'),

                    ]),
                ]),
                Section::make('Detail Peminjaman')
                    ->schema([

                        Repeater::make('borrowingDetail')
                            ->label('Buku yang Dipinjam')
                            ->relationship()
                            ->schema([
                                Select::make('book_id')
                                    ->label('Buku')
                                    ->options(\App\Models\Book::pluck('title', 'id'))
                                    ->searchable()
                                    ->placeholder('Pilih buku yang dipinjam')
                                    ->required()
                                    ->helperText('Pilih buku yang dipinjam dari daftar buku yang tersedia')
                                    ->live()
                                    ->afterStateUpdated(fn($state, $set) => [
                                        $set('stock', \App\Models\Book::find($state)?->stock ?? 0),
                                        $set('quantity', 1)
                                    ]),
                                TextInput::make('stock')
                                    ->label('Stok Tersedia')
                                    ->disabled()
                                    ->dehydrated()
                                    ->helperText('Stok buku saat ini, akan diperbarui secara otomatis berdasarkan pilihan buku'),
                                TextInput::make('quantity')
                                    ->label('QTY')
                                    ->disabled()
                                    ->dehydrated(),
                            ])
                            ->addActionLabel('Tambah Buku')
                            ->rules(['distinct']),

                    ])
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
            'edit' => Pages\EditBorrowing::route('/{record}/edit'),
        ];
    }
}
