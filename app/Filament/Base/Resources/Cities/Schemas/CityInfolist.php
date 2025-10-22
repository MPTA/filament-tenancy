<?php

namespace App\Filament\Base\Resources\Cities\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class CityInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('City Information')
                    ->schema([
                        TextEntry::make('name')
                            ->label('City Name')
                            ->size('lg')
                            ->weight('bold'),
                        TextEntry::make('province.name')
                            ->label('Province')
                            ->badge()
                            ->color('success'),
                        TextEntry::make('code')
                            ->label('City Code')
                            ->badge()
                            ->color('info')
                            ->copyable()
                            ->placeholder('-'),
                        IconEntry::make('has_code')
                            ->label('Has Code')
                            ->boolean(),
                        TextEntry::make('native')
                            ->label('Native Name')
                            ->placeholder('-'),
                    ])
                    ->columns(2),

                Section::make('Geographic Information')
                    ->schema([
                        TextEntry::make('latitude')
                            ->label('Latitude')
                            ->placeholder('-')
                            ->copyable(),
                        TextEntry::make('longitude')
                            ->label('Longitude')
                            ->placeholder('-')
                            ->copyable(),
                        TextEntry::make('timezone')
                            ->label('Timezone')
                            ->badge()
                            ->color('warning')
                            ->placeholder('-'),
                        TextEntry::make('wiki_data_id')
                            ->label('WikiData ID')
                            ->placeholder('-')
                            ->copyable()
                            ->url(fn ($record) => $record->wiki_data_id ? "https://www.wikidata.org/wiki/{$record->wiki_data_id}" : null)
                            ->openUrlInNewTab(),
                    ])
                    ->columns(2)
                    ->collapsible(),

                Section::make('Statistics')
                    ->schema([
                        TextEntry::make('districts_count')
                            ->label('Districts')
                            ->badge()
                            ->color('success')
                            ->state(fn ($record) => $record->districts()->count()),
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
                    ->columns(3),

                Section::make('System Information')
                    ->schema([
                        TextEntry::make('id')
                            ->label('UUID')
                            ->copyable(),
                        TextEntry::make('created_at')
                            ->label('Created At')
                            ->dateTime(),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime(),
                    ])
                    ->columns(3)
                    ->collapsible(),
            ]);
    }
}
