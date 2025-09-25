<?php

namespace App\Filament\Resources\Users\Pages;

use App\Filament\Resources\Users\UserResource;
use App\Models\User;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListUsers extends ListRecords
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos los usuarios')
                ->icon('heroicon-m-queue-list')
                ->badge(User::query()->count()),
            'active' => Tab::make('Usuarios activos')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->icon('heroicon-m-check-circle')
                ->iconPosition(IconPosition::After)
                ->badge(User::query()->where('is_active', true)->count())
                ->badgeColor('success'),
            'inactive' => Tab::make('Usuarios inactivos')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->icon('heroicon-m-x-circle')
                ->badge(User::query()->where('is_active', false)->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }
}
