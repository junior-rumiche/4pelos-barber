<?php

namespace App\Filament\Resources\Customers\Pages;

use App\Filament\Resources\Customers\CustomerResource;
use App\Models\Customer;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListCustomers extends ListRecords
{
    protected static string $resource = CustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nuevo cliente'),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todos los clientes')
                ->icon('heroicon-m-queue-list')
                ->badge(Customer::query()->count()),
            'with_phone' => Tab::make('Con teléfono')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNotNull('phone'))
                ->icon('heroicon-m-device-phone-mobile')
                ->iconPosition(IconPosition::After)
                ->badge(Customer::query()->whereNotNull('phone')->count())
                ->badgeColor('success'),
            'without_phone' => Tab::make('Sin teléfono')
                ->modifyQueryUsing(fn(Builder $query) => $query->whereNull('phone'))
                ->icon('heroicon-m-x-circle')
                ->badge(Customer::query()->whereNull('phone')->count())
                ->badgeColor('warning'),
        ];
    }

    public function getDefaultActiveTab(): string | int | null
    {
        return 'all';
    }
}
