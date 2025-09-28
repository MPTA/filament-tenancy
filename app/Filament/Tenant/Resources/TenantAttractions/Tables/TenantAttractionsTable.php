<?php

namespace App\Filament\Tenant\Resources\TenantAttractions\Tables;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\ViewAction;
use Filament\Tables\Enums\FiltersLayout;

class TenantAttractionsTable
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
                TextColumn::make('type')
                    ->label('Type')
                    ->badge()
                    ->color('info'),
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
                SelectFilter::make('type')
                    ->label('Type')
                    ->options([
                        'historical' => 'Historical',
                        'natural' => 'Natural',
                        'cultural' => 'Cultural',
                        'religious' => 'Religious',
                        'entertainment' => 'Entertainment',
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