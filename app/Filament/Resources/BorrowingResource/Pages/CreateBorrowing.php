<?php

namespace App\Filament\Resources\BorrowingResource\Pages;

use App\Filament\Resources\BorrowingResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Models\Book;
use Filament\Notifications\Notification;

class CreateBorrowing extends CreateRecord
{
    protected static string $resource = BorrowingResource::class;

    public function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    public function mutateFormDataBeforeCreate(array $data): array
    {
        $data['status'] = 'borrowed';
        return $data;
    }

    public function beforeCreate(): void
    {
        $state = $this->form->getRawState();
        $books = $state['book_list'] ?? [];

        // ❌ Cek buku duplikat
        $bookIds = collect($books)->pluck('book_id')->filter();

        if ($bookIds->count() !== $bookIds->unique()->count()) {
            Notification::make()
                ->title('Tidak boleh meminjam buku yang sama!')
                ->danger()
                ->send();
            $this->halt();
            return;
        }

        // ❌ Cek stok tiap buku
        foreach ($books as $item) {
            $book = Book::find($item['book_id']);
            if (!$book || $book->stock < $item['quantity']) {
                Notification::make()
                    ->title("Stok buku \"{$book?->title}\" tidak mencukupi!")
                    ->danger()
                    ->send();
                $this->halt();
                return;
            }
        }
    }

    public function afterCreate(): void
    {
        $state = $this->form->getRawState();
        $books = $state['book_list'] ?? [];

        foreach ($books as $item) {
            $book = Book::find($item['book_id']);
            if ($book) {
                $book->decrement('stock', $item['quantity']);
            }
        }

        Notification::make()
            ->title('Peminjaman berhasil dibuat')
            ->success()
            ->send();
    }
}