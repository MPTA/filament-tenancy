<?php

namespace App\Filament\Base\Resources\Countries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CountryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Country Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('Country Name'),
                        TextEntry::make('code')
                            ->label('Country Code')
                            ->badge()
                            ->color('info')
                            ->copyable(),
                    ])
                    ->columns(2),

                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('provinces_count')
                            ->label('Provinces')
                            ->badge()
                            ->color('success')
                            ->state(fn ($record) => $record->provinces()->count()),
                        TextEntry::make('cities_count')
                            ->label('Cities')
                            ->badge()
                            ->color('info')
                            ->state(fn ($record) => $record->cities()->count()),
                        TextEntry::make('attractions_count')
                            ->label('Attractions')
                            ->badge()
                            ->color('warning')
                            ->state(fn ($record) => $record->attractions()->count()),
                        TextEntry::make('accommodations_count')
                            ->label('Accommodations')
                            ->badge()
                            ->color('primary')
                            ->state(fn ($record) => $record->accommodations()->count()),
                    ])
                    ->columns(2),

                Section::make('System Information')
                    ->schema([
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(2)
                    ->collapsible(),
            ]);
    }
}
