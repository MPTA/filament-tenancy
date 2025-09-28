<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Schemas\Schema;

class TenantAccommodationInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Section::make('Basic Information')
                    ->description('Accommodation basic details')
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label('Name')
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('star_rating')
                                    ->label('Star Rating')
                                    ->badge()
                                    ->color('warning')
                                    ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : 'No rating'),
                            ]),
                        TextEntry::make('content')
                            ->label('Description')
                            ->html()
                            ->columnSpanFull(),
                        IconEntry::make('is_active')
                            ->label('Active')
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make('Location Information')
                    ->description('Accommodation location details')
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name')
                                    ->label('Country')
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('city.name')
                                    ->label('City')
                                    ->badge()
                                    ->color('success'),
                            ]),
                        TextEntry::make('address')
                            ->label('Address')
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label('Latitude')
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('longitude')
                                    ->label('Longitude')
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible(),

            ]);
    }
}