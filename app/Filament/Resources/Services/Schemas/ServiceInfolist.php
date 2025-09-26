<?php

namespace App\Filament\Resources\Services\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ServiceInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('Nombre')
                    ->columnSpanFull(),
                TextEntry::make('price')
                    ->label('Precio')
                    ->money('PEN'),
                IconEntry::make('is_active')
                    ->label('Activo')
                    ->boolean(),
                TextEntry::make('description')
                    ->label('Descripción')
                    ->placeholder('Sin descripción')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Creado en')
                    ->dateTime('d/m/Y h:i A'),
                TextEntry::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime('d/m/Y h:i A'),
            ]);
    }
}
