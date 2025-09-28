<?php

namespace App\Filament\Resources\Services\Tables;

use App\Models\Service;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;

class ServicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('price')
                    ->label('Precio')
                    ->money('PEN')
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label('Creado en')
                    ->formatStateUsing(fn($state) => $state->format('d/m/Y h:i A'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->formatStateUsing(fn($state) => $state->format('d/m/Y h:i A'))
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make()
                        ->label('Ver')
                        ->icon('heroicon-o-eye'),
                    EditAction::make()
                        ->label('Editar')
                        ->icon('heroicon-o-pencil'),
                    Action::make('toggle_status')
                        ->label(fn($record) => $record->is_active ? 'Desactivar' : 'Activar')
                        ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn($record) => $record->is_active ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                        })
                        ->successNotificationTitle(fn($record) => $record->is_active ? 'Servicio desactivado' : 'Servicio activado')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => $record->is_active ? 'Desactivar servicio' : 'Activar servicio')
                        ->modalDescription(
                            fn($record) => $record->is_active
                                ? '¿Estás seguro de que deseas desactivar este servicio? Los clientes no podrán verlo.'
                                : '¿Estás seguro de que deseas activar este servicio? Los clientes podrán verlo nuevamente.'
                        )
                        ->modalSubmitActionLabel(fn($record) => $record->is_active ? 'Desactivar' : 'Activar'),
                ])
                    ->label('Acciones')
                    ->icon('heroicon-o-ellipsis-vertical')
                    ->color('gray')
                    ->button(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->visible(fn(): bool => Gate::allows('deleteAny', Service::class))
                        ->authorize('deleteAny', Service::class),
                ]),
            ]);
    }
}
