<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Tables\Enums\FiltersLayout;

class TenantAccommodationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('star_rating')
                    ->label('Star Rating')
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : 'No rating'),

                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary'),
                TextColumn::make('tenant_prices_count')
                    ->label('Tenant Prices')
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($record) {
                        return ($record->tenant_prices_count ?? 0) . ' prices';
                    }),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('star_rating')
                    ->label('Star Rating')
                    ->options([
                        1 => '1 Star',
                        2 => '2 Stars', 
                        3 => '3 Stars',
                        4 => '4 Stars',
                        5 => '5 Stars',
                    ])
                    ->multiple(),
            ])
            ->filtersLayout(FiltersLayout::AboveContent)
            ->defaultSort('name', 'asc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}