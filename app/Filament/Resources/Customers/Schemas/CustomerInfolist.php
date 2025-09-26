<?php

namespace App\Filament\Resources\Customers\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class CustomerInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('full_name')
                    ->label('Nombre completo')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->label('Teléfono')
                    ->placeholder('Sin registrar')
                    ->columnSpanFull(),
                TextEntry::make('created_at')
                    ->label('Registrado en')
                    ->dateTime('d/m/Y h:i A'),
                TextEntry::make('updated_at')
                    ->label('Actualizado en')
                    ->dateTime('d/m/Y h:i A'),
            ]);
    }
}
