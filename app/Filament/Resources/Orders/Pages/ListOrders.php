<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListOrders extends ListRecords
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nueva orden')
                ->icon('heroicon-o-plus'),
        ];
    }

    /**
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('Todas')
                ->badge(fn(): int => Order::query()->count()),
            'pending' => Tab::make('Pendiente de pago')
                ->badge(fn(): int => Order::query()->where('status', Order::STATUS_PENDING)->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->where('status', Order::STATUS_PENDING)),
            'in_progress' => Tab::make('En progreso')
                ->badge(fn(): int => Order::query()->where('status', Order::STATUS_IN_PROGRESS)->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->where('status', Order::STATUS_IN_PROGRESS)),
            'paid' => Tab::make('Pagado')
                ->badge(fn(): int => Order::query()->where('status', Order::STATUS_PAID)->count())
                ->modifyQueryUsing(fn(Builder $query): Builder => $query->where('status', Order::STATUS_PAID)),
        ];
    }
}
