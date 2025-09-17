<?php

namespace App\Filament\Base\Resources\Airports\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AirportInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Airport Details')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Airport Name')
                            ->weight('bold')
                            ->size('lg'),
                        
                        TextEntry::make('iata_code')
                            ->label('IATA Code')
                            ->badge()
                            ->color('primary')
                            ->copyable()
                            ->copyMessage('IATA code copied')
                            ->copyMessageDuration(1500),
                        
                        TextEntry::make('city.name')
                            ->label('City')
                            ->placeholder('No city assigned')
                            ->icon('heroicon-o-map-pin'),
                        
                        TextEntry::make('city.province.name')
                            ->label('Province/State')
                            ->placeholder('No province assigned')
                            ->icon('heroicon-o-building-office'),
                        
                        TextEntry::make('city.province.country.name')
                            ->label('Country')
                            ->placeholder('No country assigned')
                            ->icon('heroicon-o-flag'),
                    ])
                    ->columns(2),
                
                Section::make('System Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('ID')
                            ->badge()
                            ->color('gray'),
                        
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime('M j, Y g:i A')
                            ->placeholder('Not available')
                            ->icon('heroicon-o-calendar'),
                        
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime('M j, Y g:i A')
                            ->placeholder('Not available')
                            ->icon('heroicon-o-pencil'),
                    ])
                    ->columns(3)
                    ->collapsible()
            ]);
    }
}

