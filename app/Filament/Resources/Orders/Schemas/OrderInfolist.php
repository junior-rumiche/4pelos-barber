<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use App\Models\OrderService;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\ViewEntry;
use Filament\Schemas\Schema;

class OrderInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('Código')
                    ->formatStateUsing(fn($state) => sprintf('#%04d', $state))
                    ->columnSpanFull(),
                TextEntry::make('customer.full_name')
                    ->label('Cliente'),
                TextEntry::make('customer.phone')
                    ->label('Teléfono')
                    ->placeholder('Sin teléfono'),
                TextEntry::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($state) => Order::STATUS_COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn($state) => Order::STATUS_LABELS[$state] ?? 'Desconocido'),
                TextEntry::make('total_amount')
                    ->label('Total')
                    ->money('PEN'),
                TextEntry::make('createdBy.name')
                    ->label('Registrado por')
                    ->placeholder('Sin asignar'),
                TextEntry::make('paymentProcessedBy.name')
                    ->label('Pago procesado por')
                    ->placeholder('Pendiente'),
                TextEntry::make('paid_at')
                    ->label('Pagado en')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendiente'),
                TextEntry::make('created_at')
                    ->label('Creado en')
                    ->dateTime('d/m/Y H:i'),
                TextEntry::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime('d/m/Y H:i'),
                RepeatableEntry::make('items')
                    ->label('Servicios incluidos')
                    ->contained(false)
                    ->grid(1)
                    ->extraAttributes(['class' => 'space-y-4'])
                    ->schema([
                        ViewEntry::make('service-card')
                            ->view('filament.orders.infolists.service-card')
                            ->viewData(fn(OrderService $item): array => [
                                'serviceName' => $item->service?->name ?? 'Servicio sin nombre',
                                'quantity' => max(1, (int) $item->quantity),
                                'unitPrice' => (float) $item->price_at_time_of_order,
                                'subtotal' => round((float) $item->price_at_time_of_order * max(1, (int) $item->quantity), 2),
                            ])
                            ->extraEntryWrapperAttributes([
                                'class' => 'fi-order-service-card',
                            ])
                            ->columnSpanFull(),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
