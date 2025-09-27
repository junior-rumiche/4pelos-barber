<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

class UserForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                FileUpload::make('profile_photo')
                    ->label('Foto de perfil')
                    ->image()
                    ->directory('profile-photos')
                    ->columnSpanFull(),
                TextInput::make('name')
                    ->label('Nombre')
                    ->required(),
                TextInput::make('email')
                    ->label('Correo')
                    ->email()
                    ->required(),
                Toggle::make('is_active')
                    ->label('Activo')
                    ->default(true),
                Select::make('roles')
                    ->label('Roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload()
                    ->searchable()
                    ->helperText('Selecciona uno o más roles a asignar al usuario.')
                    ->columnSpanFull(),
                DateTimePicker::make('email_verified_at')
                    ->label('Verificado en'),
                TextInput::make('password')
                    ->label('Contraseña')
                    ->password()
                    ->required(),
            ]);
    }
}
