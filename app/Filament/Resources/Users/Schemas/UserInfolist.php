<?php

namespace App\Filament\Resources\Users\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class UserInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                ImageEntry::make('profile_photo_url')
                    ->label('Foto de perfil')
                    ->circular()
                    ->columnSpanFull(),
                TextEntry::make('name')
                    ->label('Nombre')
                    ->columnSpanFull(),
                TextEntry::make('email')
                    ->label('Correo'),
                IconEntry::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextEntry::make('email_verified_at')
                    ->label('Verificado en')
                    ->dateTime('d/m/Y h:i A')
                    ->placeholder('Sin verificar'),
                TextEntry::make('created_at')
                    ->label('Creado en')
                    ->dateTime('d/m/Y h:i A'),
                TextEntry::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime('d/m/Y h:i A'),
            ]);
    }
}
