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
                Section::make(__('tenant-accommodations.sections.basic_information.title'))
                    ->description(__('tenant-accommodations.sections.basic_information.description'))
                    ->icon('heroicon-o-building-office')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('name')
                                    ->label(__('common-fields.name'))
                                    ->badge()
                                    ->color('primary'),
                                TextEntry::make('star_rating')
                                    ->label(__('common-fields.star_rating'))
                                    ->badge()
                                    ->color('warning')
                                    ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : __('tenant-accommodations.messages.no_rating')),
                            ]),
                        TextEntry::make('content')
                            ->label(__('common-fields.description'))
                            ->html()
                            ->columnSpanFull(),
                        IconEntry::make('is_active')
                            ->label(__('common-fields.is_active'))
                            ->boolean()
                            ->trueIcon('heroicon-o-check-circle')
                            ->falseIcon('heroicon-o-x-circle')
                            ->trueColor('success')
                            ->falseColor('danger')
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                Section::make(__('tenant-accommodations.sections.location_information.title'))
                    ->description(__('tenant-accommodations.sections.location_information.description'))
                    ->icon('heroicon-o-map-pin')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('country.name')
                                    ->label(__('common-fields.country'))
                                    ->badge()
                                    ->color('info'),
                                TextEntry::make('city.name')
                                    ->label(__('common-fields.city'))
                                    ->badge()
                                    ->color('success'),
                            ]),
                        TextEntry::make('address')
                            ->label(__('common-fields.address'))
                            ->columnSpanFull(),
                        Grid::make(2)
                            ->schema([
                                TextEntry::make('latitude')
                                    ->label(__('common-fields.latitude'))
                                    ->badge()
                                    ->color('gray'),
                                TextEntry::make('longitude')
                                    ->label(__('common-fields.longitude'))
                                    ->badge()
                                    ->color('gray'),
                            ]),
                    ])
                    ->collapsible(),

            ]);
    }
}