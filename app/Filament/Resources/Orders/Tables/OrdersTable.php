<?php

namespace App\Filament\Resources\Orders\Tables;

use App\Filament\Resources\Orders\OrderResource;
use App\Models\Order;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

class OrdersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn(Builder $query) => $query->with(['customer', 'services', 'createdBy', 'paymentProcessedBy']))
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('id')
                    ->label('Código')
                    ->formatStateUsing(fn($state) => sprintf('#%04d', $state))
                    ->sortable(),
                TextColumn::make('customer.full_name')
                    ->label('Cliente')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('services.name')
                    ->label('Servicios')
                    ->badge()
                    ->listWithLineBreaks()
                    ->limitList(3)
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn($state) => Order::STATUS_COLORS[$state] ?? 'gray')
                    ->formatStateUsing(fn($state) => OrderResource::getStatusOptions()[$state] ?? 'Desconocido')
                    ->sortable(),
                TextColumn::make('total_amount')
                    ->label('Total')
                    ->money('PEN')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('paid_at')
                    ->label('Pagado en')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Pendiente')
                    ->sortable(),
                TextColumn::make('createdBy.name')
                    ->label('Registrado por')
                    ->badge()
                    ->color('info'),
                TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([])
            ->recordActions([
                ViewAction::make()
                    ->label('Ver')
                    ->icon('heroicon-o-eye'),
                ActionGroup::make([
                    EditAction::make()
                        ->label('Editar')
                        ->icon('heroicon-o-pencil'),
                    Action::make('mark-as-pending')
                        ->label('Pasar a pendiente')
                        ->icon('heroicon-o-arrow-uturn-left')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->visible(fn(Order $record): bool => (int) $record->status === Order::STATUS_IN_PROGRESS)
                        ->action(function (Order $record): void {
                            $record->markAsPending();
                        })
                        ->successNotificationTitle('Orden actualizada a pendiente de pago'),
                    Action::make('mark-as-paid')
                        ->label('Marcar como pagado')
                        ->icon('heroicon-o-banknotes')
                        ->color('success')
                        ->requiresConfirmation()
                        ->visible(fn(Order $record): bool => (int) $record->status === Order::STATUS_PENDING)
                        ->action(function (Order $record): void {
                            if ($record->isPaid()) {
                                return;
                            }

                            $record->markAsPaid(Auth::id());
                        })
                        ->successNotificationTitle('Orden marcada como pagada'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->color('gray')
                    ->button()
                    ->visible(fn(Order $record): bool => ! $record->isPaid()),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
