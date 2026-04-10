<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Actions;
use Filament\Forms\Form;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\TextColumn;
use Filament\Notifications\Notification;

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

                    Actions\Action::make('approve_borrow')
            ->label('Setujui Peminjaman')
            ->icon('heroicon-o-check-circle')
            ->color('success')
            ->visible(fn() => $this->record->isWaiting())
            ->requiresConfirmation()
            ->modalHeading('Setujui Peminjaman')
            ->modalDescription('Apakah anda yakin ingin menyetujui peminjaman ini?')
            ->modalSubmitActionLabel('Ya, Setujui')
            ->action(function (): void {
                // Kurangi stok buku
                foreach ($this->record->borrowingDetail as $detail) {
                    $detail->book->decrement('stock', $detail->quantity);
                }

                $this->record->update(['status' => 'borrowed']);

                Notification::make()
                    ->success()
                    ->title('Peminjaman disetujui!')
                    ->send();

                $this->refreshFormData(['status']);
            }),

            Actions\Action::make('approve_return')
                ->label('Approve Pengembalian')
                ->visible(fn() => $this->record->status === 'borrowed'  || $this->record->status === 'pending_return')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->form([
                    TextInput::make('damage_fine')
                        ->label('Denda Kerusakan')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('Masukkan denda jika ada')
                        ->default(0),

                    TextInput::make('lost_fine')
                        ->label('Denda Kehilangan')
                        ->numeric()
                        ->prefix('Rp')
                        ->placeholder('Masukkan denda jika ada')
                        ->default(0),
                ])
                ->modalHeading('Approve Pengembalian Buku')
                ->modalDescription('Periksa kondisi buku dan isi denda jika diperlukan.')
                ->modalSubmitActionLabel('Approve & Simpan')
                ->action(function (array $data): void {
                    $returnDate = now();
                    $isLate = $returnDate->startOfDay()->gt($this->record->due_date->startOfDay());

                    $this->record->update([
                        'status' => $isLate ? 'returned_late' : 'returned',
                        'return_date' => $returnDate,
                        'damage_fine' => $data['damage_fine'] ?? 0,
                        'lost_fine' => $data['lost_fine'] ?? 0,
                    ]);

                    // Kembalikan stok buku
                    foreach ($this->record->borrowingDetail as $detail) {
                        $detail->book->increment('stock', $detail->quantity);
                    }

                    $this->record->refresh();

                    Notification::make()
                        ->success()
                        ->title('Pengembalian disetujui!')
                        ->body('Total denda: Rp ' . number_format($this->record->fine, 0, ',', '.'))
                        ->send();

                    $this->refreshFormData([
                        'status',
                        'return_date',
                        'damage_fine',
                        'lost_fine',
                    ]);
                }),
        ];
    }
}