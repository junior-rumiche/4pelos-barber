<?php

namespace App\Filament\Resources\Users\Tables;

use App\Models\User;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;

class UsersTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('profile_photo')
                    ->label('Foto de perfil')
                    ->getStateUsing(fn($record) => $record->profile_photo_url)
                    ->circular(),
                TextColumn::make('name')
                    ->label('Nombre')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('Correo')
                    ->searchable(),
                IconColumn::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextColumn::make('email_verified_at')
                    ->label('Verificado en')
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label('Creado en')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime()
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
                    Action::make('change_password')
                        ->label('Cambiar contraseña')
                        ->icon('heroicon-o-key')
                        ->form([
                            TextInput::make('password')
                                ->label('Nueva contraseña')
                                ->password()
                                ->revealable()
                                ->required()
                                ->rules(['confirmed', Password::defaults()]),
                            TextInput::make('password_confirmation')
                                ->label('Confirmar contraseña')
                                ->password()
                                ->revealable()
                                ->required()
                                ->dehydrated(false),
                        ])
                        ->action(function (array $data, $record): void {
                            $record->update([
                                'password' => Hash::make($data['password']),
                            ]);
                        })
                        ->successNotificationTitle('Contraseña actualizada correctamente'),
                    Action::make('toggle_status')
                        ->label(fn($record) => $record->is_active ? 'Desactivar' : 'Activar')
                        ->icon(fn($record) => $record->is_active ? 'heroicon-o-eye-slash' : 'heroicon-o-eye')
                        ->color(fn($record) => $record->is_active ? 'danger' : 'success')
                        ->action(function ($record) {
                            $record->update(['is_active' => !$record->is_active]);
                        })
                        ->successNotificationTitle(fn($record) => $record->is_active ? 'Usuario desactivado' : 'Usuario activado')
                        ->requiresConfirmation()
                        ->modalHeading(fn($record) => $record->is_active ? 'Desactivar usuario' : 'Activar usuario')
                        ->modalDescription(
                            fn($record) => $record->is_active
                                ? '¿Estás seguro de que deseas desactivar este usuario?'
                                : '¿Estás seguro de que deseas activar este usuario?'
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
                        ->visible(fn(): bool => Gate::allows('deleteAny', User::class))
                        ->authorize('deleteAny', User::class),
                ]),
            ]);
    }
}
