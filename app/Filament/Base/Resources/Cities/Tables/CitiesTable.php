<?php

namespace App\Filament\Base\Resources\Cities\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

class CitiesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('City Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('province.name')
                    ->label('Province')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success'),
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->copyable()
                    ->placeholder('-'),
                IconColumn::make('has_code')
                    ->label('Has Code')
                    ->boolean()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('native')
                    ->label('Native Name')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('timezone')
                    ->label('Timezone')
                    ->searchable()
                    ->placeholder('-')
                    ->toggleable(),
                TextColumn::make('districts_count')
                    ->label('Districts')
                    ->counts('districts')
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
                SelectFilter::make('province')
                    ->label('Province')
                    ->relationship('province', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('has_code')
                    ->label('Has Code')
                    ->query(fn (Builder $query): Builder => $query->where('has_code', true)),
                Filter::make('has_timezone')
                    ->label('Has Timezone')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('timezone')),
                Filter::make('has_coordinates')
                    ->label('Has Coordinates')
                    ->query(fn (Builder $query): Builder => $query->whereNotNull('latitude')->whereNotNull('longitude')),
                Filter::make('has_districts')
                    ->label('Has Districts')
                    ->query(fn (Builder $query): Builder => $query->has('districts')),
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
