<?php

namespace App\Filament\Base\Resources\Countries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CountriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Country Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable(),
                TextColumn::make('provinces_count')
                    ->label('Provinces')
                    ->counts('provinces')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('cities_count')
                    ->label('Cities')
                    ->counts('cities')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('attractions_count')
                    ->label('Attractions')
                    ->counts('attractions')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('accommodations_count')
                    ->label('Accommodations')
                    ->counts('accommodations')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                Filter::make('has_provinces')
                    ->label('Has Provinces')
                    ->query(fn (Builder $query): Builder => $query->has('provinces')),
                Filter::make('has_cities')
                    ->label('Has Cities')
                    ->query(fn (Builder $query): Builder => $query->has('cities')),
                Filter::make('has_attractions')
                    ->label('Has Attractions')
                    ->query(fn (Builder $query): Builder => $query->has('attractions')),
                Filter::make('has_accommodations')
                    ->label('Has Accommodations')
                    ->query(fn (Builder $query): Builder => $query->has('accommodations')),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name');
    }
}
