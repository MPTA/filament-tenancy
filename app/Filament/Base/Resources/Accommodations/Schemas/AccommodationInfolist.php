<?php

namespace App\Filament\Base\Resources\Accommodations\Schemas;

use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class AccommodationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Accommodation Name')
                                    ->weight('bold')
                                    ->size('lg'),
                                TextEntry::make('star_rating')
                                    ->label('Star Rating')
                                    ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : 'No rating')
                                    ->badge()
                                    ->color('warning'),
                            ]),
                        TextEntry::make('content')
                            ->label('Description')
                            ->columnSpanFull()
                            ->placeholder('No description available'),
                        TextEntry::make('address')
                            ->label('Address')
                            ->columnSpanFull()
                            ->placeholder('No address available'),
                    ]),
                
                Section::make('Location Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name')
                                    ->label('Country')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('province.name')
                                    ->label('Province')
                                    ->badge()
                                    ->color('info'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('city.name')
                                    ->label('City')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('district.name')
                                    ->label('District')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('No district'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('Not set'),
                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('Not set'),
                            ]),
                    ]),
                
                Section::make('Additional Information')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('external_id')
                                    ->label('External ID')
                                    ->badge()
                                    ->color('gray')
                                    ->placeholder('Not set'),
                                IconEntry::make('is_active')
                                    ->label('Status')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-check-circle')
                                    ->falseIcon('heroicon-o-x-circle')
                                    ->trueColor('success')
                                    ->falseColor('danger'),
                            ]),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('prices_count')
                                    ->label('Number of Prices')
                                    ->counts('prices')
                                    ->badge()
                                    ->color('success'),
                                TextEntry::make('created_at')
                                    ->label('Created At')
                                    ->dateTime()
                                    ->placeholder('-'),
                            ]),
                        TextEntry::make('updated_at')
                            ->label('Updated At')
                            ->dateTime()
                            ->placeholder('-'),
                    ]),
            ]);
    }
}
