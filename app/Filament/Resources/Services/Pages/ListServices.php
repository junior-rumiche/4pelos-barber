<?php

namespace App\Filament\Resources\Services\Pages;

use App\Filament\Resources\Services\ServiceResource;
use App\Models\Service;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListServices extends ListRecords
{
    protected static string $resource = ServiceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos los servicios')
                ->icon('heroicon-m-queue-list')
                ->badge(Service::query()->count()),
            'active' => Tab::make('Servicios activos')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', true))
                ->icon('heroicon-m-check-circle')
                ->iconPosition(IconPosition::After)
                ->badge(Service::query()->where('is_active', true)->count())
                ->badgeColor('success'),
            'inactive' => Tab::make('Servicios inactivos')
                ->modifyQueryUsing(fn(Builder $query) => $query->where('is_active', false))
                ->icon('heroicon-m-x-circle')
                ->badge(Service::query()->where('is_active', false)->count())
                ->badgeColor('danger'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }
}
