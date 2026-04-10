<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Actions;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\TextInput;

class ViewBorrowing extends ViewRecord
{
    protected static string $resource = BorrowingResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Informasi Peminjam')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                TextEntry::make('user.name')
                                    ->label('Peminjam (Siswa)')
                                    ->placeholder('-')
                                    ->visible(fn($record) => filled($record->user_id)),

                                TextEntry::make('visitor.name')
                                    ->label('Pengunjung (Umum)')
                                    ->placeholder('-')
                                    ->visible(fn($record) => filled($record->visitor_id)),

                                TextEntry::make('status')
                                    ->badge()
                                    ->color(fn($state) => match ($state) {
                                        'borrowed' => 'primary',
                                        'waiting' => 'info',
                                        'returned_late' => 'danger',
                                        'pending_return' => 'info',
                                        'returned' => 'success',
                                        default => 'gray',
                                    })
                                    ->formatStateUsing(fn($state) => match ($state) {
                                        'borrowed' => 'Dipinjam',
                                        'returned_late' => 'Dikembalikan Terlambat',
                                        'pending_return' => 'Menunggu Konfirmasi',
                                        'waiting' => 'Menunggu Dikonformasi',
                                        'returned' => 'Dikembalikan',
                                        default => $state
                                    }),
                            ]),

                        Grid::make(3)
                            ->schema([
                                TextEntry::make('borrow_date')
                                    ->label('Tanggal Pinjam')
                                    ->date('d M Y'),

                                TextEntry::make('due_date')
                                    ->label('Tanggal Jatuh Tempo')
                                    ->date('d M Y'),

                                TextEntry::make('return_date')
                                    ->label('Tanggal Dikembalikan')
                                    ->date('d M Y')
                                    ->placeholder('Belum dikembalikan'),
                            ]),

                        TextEntry::make('fine')
                            ->label('Total Denda')
                            ->money('idr')
                            ->color('danger')
                            ->weight('bold'),
                    ]),

                Section::make('Daftar Buku Yang Dipinjam')
                    ->schema([
                        RepeatableEntry::make('borrowingDetail') // Sesuaikan dengan nama relasi di Model
                            ->label('')
                            ->schema([
                                Grid::make(3)
                                    ->schema([
                                        TextEntry::make('book.title')
                                            ->label('Judul Buku'),
                                        
                                        TextEntry::make('quantity')
                                            ->label('Jumlah')
                                            ,

                                        TextEntry::make('book.isbn')
                                            ->label('ISBN')
                                            ->placeholder('-'),
                                    ]),
                            ])
                            ->columns(1),
                    ]),
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }
}