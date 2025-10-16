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
                    ->label(__('common-fields.name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('type')
                    ->label(__('common-fields.type'))
                    ->badge()
                    ->color('info'),
                TextColumn::make('city.name')
                    ->label(__('common-fields.city'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('district.name')
                    ->label(__('common-fields.district'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('secondary'),
                TextColumn::make('tenant_prices_count')
                    ->label(__('tenant-attractions.columns.tenant_prices'))
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($record) {
                        return __('tenant-attractions.messages.prices_count', ['count' => $record->tenant_prices_count ?? 0]);
                    }),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label(__('common-fields.city'))
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('type')
                    ->label(__('common-fields.type'))
                    ->options([
                        'historical' => __('tenant-attractions.filters.type.historical'),
                        'natural' => __('tenant-attractions.filters.type.natural'),
                        'cultural' => __('tenant-attractions.filters.type.cultural'),
                        'religious' => __('tenant-attractions.filters.type.religious'),
                        'entertainment' => __('tenant-attractions.filters.type.entertainment'),
                    ])
                    ->multiple(),
            ])
            ->emptyStateHeading(__('tenant-attractions.empty_state.heading'))
            ->emptyStateDescription(__('tenant-attractions.empty_state.description'))
            ->filtersLayout(FiltersLayout::AboveContent)
            ->defaultSort('name', 'asc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}