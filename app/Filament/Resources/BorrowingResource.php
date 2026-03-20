<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowingResource\Pages;
use App\Filament\Resources\BorrowingResource\RelationManagers;
use App\Models\Borrowing;
use Filament\Forms;
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
class BorrowingResource extends Resource
{
    protected static ?string $model = Borrowing::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                //
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
                    fn($state)=> match ($state){
                        'borrowed' => 'primary',
                        'waiting' => 'info',
                        'returned_late' => 'danger',
                        'pending_return' => 'info',
                        'returned' => 'success'
                    }
                )->formatStateUsing(
                    fn($state) => match ($state){
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
                    ->visible(fn($record)=> $record->isPendingReturn())
                    ->action(function ($record){

                        DB::transaction(function () use ($record){
                            $record->update([
                                'return_date' =>now(),
                                'status' => $record->isOverdue() ? 'returned_late' : 'returned'
                            ]);

                            foreach($record->borrowingDetail as $detail){
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
