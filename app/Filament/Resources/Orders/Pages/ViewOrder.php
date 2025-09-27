<?php

namespace App\Filament\Resources\Orders\Pages;

use App\Filament\Resources\Orders\OrderResource;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewOrder extends ViewRecord
{
    protected static string $resource = OrderResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
            Action::make('print')
                ->label('Imprimir')
                ->icon('heroicon-o-printer')
                ->color('gray')
                ->url(fn(): string => route('orders.print', $this->record))
                ->openUrlInNewTab(),
        ];
    }
}
