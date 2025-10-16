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
                    ->label(__('common-fields.name'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary'),
                TextColumn::make('star_rating')
                    ->label(__('common-fields.star_rating'))
                    ->badge()
                    ->color('warning')
                    ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : __('tenant-accommodations.messages.no_rating')),

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
                    ->label(__('tenant-accommodations.columns.tenant_prices'))
                    ->badge()
                    ->color('primary')
                    ->formatStateUsing(function ($record) {
                        return __('tenant-accommodations.messages.prices_count', ['count' => $record->tenant_prices_count ?? 0]);
                    }),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label(__('common-fields.city'))
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload()
                    ->multiple(),
                SelectFilter::make('star_rating')
                    ->label(__('common-fields.star_rating'))
                    ->options([
                        1 => __('tenant-accommodations.filters.star_rating.1'),
                        2 => __('tenant-accommodations.filters.star_rating.2'), 
                        3 => __('tenant-accommodations.filters.star_rating.3'),
                        4 => __('tenant-accommodations.filters.star_rating.4'),
                        5 => __('tenant-accommodations.filters.star_rating.5'),
                    ])
                    ->multiple(),
            ])
            ->emptyStateHeading(__('tenant-accommodations.empty_state.heading'))
            ->emptyStateDescription(__('tenant-accommodations.empty_state.description'))
            ->filtersLayout(FiltersLayout::AboveContent)
            ->defaultSort('name', 'asc')
            ->recordActions([
                ViewAction::make(),
            ]);
    }
}