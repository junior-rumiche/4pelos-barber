<?php

namespace App\Filament\Resources\Orders\Schemas;

use App\Models\Order;
use Filament\Infolists\Components\RepeatableEntry;
use Filament\Infolists\Components\TextEntry;
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
                    ->schema([
                        TextEntry::make('service.name')
                            ->label('Servicio'),
                        TextEntry::make('quantity')
                            ->label('Cantidad')
                            ->formatStateUsing(fn($state) => (int) max(1, (int) $state)),
                        TextEntry::make('price_at_time_of_order')
                            ->label('Precio unitario')
                            ->money('PEN'),
                        TextEntry::make('subtotal')
                            ->label('Subtotal')
                            ->state(function ($item): float {
                                $price = $item['price_at_time_of_order'] ?? 0;
                                $quantity = $item['quantity'] ?? 1;

                                if (is_string($price)) {
                                    $price = str_replace([' ', ','], ['', '.'], $price);
                                }

                                if (is_string($quantity)) {
                                    $quantity = str_replace([' ', ','], ['', '.'], $quantity);
                                }

                                $price = is_numeric($price) ? (float) $price : 0.0;
                                $quantity = is_numeric($quantity) ? (float) $quantity : 1.0;

                                return $price * max(1, (int) round($quantity));
                            })
                            ->money('PEN'),
                    ])
                    ->columnSpanFull(),
            ]);
    }
}
