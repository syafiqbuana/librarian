<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Filament\Resources\Components\Tab;
use App\Models\User;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }


    public function getTabs(): array
    {
        return [
            'semua' => Tab::make('Semua')
                ->badge(User::count()),

            'admin' => Tab::make('Admin')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'admin'))
                ->badge(User::where('role', 'admin')->count()),

            'siswa' => Tab::make('Siswa')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('role', 'student'))
                ->badge(User::where('role', 'student')->count()),
        ];
    }
}
