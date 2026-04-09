<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Infolist;
use Filament\Forms\Components\Card;

class ViewBorrowing extends ViewRecord
{
    protected static string $resource = BorrowingResource::class;

    protected function getHeaderWidgets(): array
    {
        return [];
    }

    protected function getFormSchema(): array
    {
        return [
            Card::make([
                Infolist::make([
                    TextInput::make('id')
                        ->label('ID Peminjaman')
                        ->disabled(),

                    TextInput::make('user.name')
                        ->label('Peminjam')
                        ->disabled(),

                    TextInput::make('status')
                        ->label('Status')
                        ->disabled(),

                    TextInput::make('created_at')
                        ->label('Tanggal Pinjam')
                        ->disabled(),
                ]),
            ]),

            Card::make([
                Infolist::make([
                    TextInput::make('borrowingDetail.0.book.title')
                        ->label('Buku 1')
                        ->disabled(),

                    TextInput::make('borrowingDetail.1.book.title')
                        ->label('Buku 2')
                        ->disabled(),

                    TextInput::make('borrowingDetail.2.book.title')
                        ->label('Buku 3')
                        ->disabled(),
                ])
            ]),
        ];
    }
}