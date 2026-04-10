<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;

class ListBorrowings extends ListRecords
{
    protected static string $resource = BorrowingResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return[
            Tab::make('semua')
                ->label('Semua'),
            Tab::make('waiting')
                ->label('Menunggu Konfirmasi')
                ->modifyQueryUsing(fn($query) => $query->where('status', ['waiting','pending_return'])),
            Tab::make('borrowed')
                ->label('Dipinjam')
                ->modifyQueryUsing(fn($query) => $query->where('status', 'borrowed')),
            Tab::make('returned')
                ->label('Dikembalikan')
                ->modifyQueryUsing(fn($query) => $query->where('status', ['returned', 'returned_late'])),
        ];
    }
}
