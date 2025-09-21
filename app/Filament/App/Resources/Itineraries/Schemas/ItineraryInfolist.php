<?php

namespace App\Filament\App\Resources\Itineraries\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class ItineraryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('id')
                    ->label('ID'),
                TextEntry::make('tenant.name')
                    ->label('Tenant'),
                TextEntry::make('travel_mode')
                    ->badge(),
                IconEntry::make('is_advanced')
                    ->boolean(),
                IconEntry::make('is_complete')
                    ->boolean(),
                IconEntry::make('is_vip')
                    ->boolean(),
                TextEntry::make('creator_user_id'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('itineraryable_type'),
                TextEntry::make('itineraryable_id'),
            ]);
    }
}
