<?php

namespace App\Filament\Base\Resources\Accommodations\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use App\Enums\StarRatingEnum;

class AccommodationsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('star_rating')
                    ->label('Rating')
                    ->formatStateUsing(fn($state) => $state ? str_repeat('★', $state) : '-')
                    ->sortable()
                    ->badge()
                    ->color('warning'),
                TextColumn::make('location')
                    ->label('Location')
                    ->formatStateUsing(fn($record) => implode(', ', array_filter([
                        $record->city?->name,
                        $record->province?->name,
                        $record->country?->name,
                    ])))
                    ->searchable(['city.name', 'province.name', 'country.name'])
                    ->sortable()
                    ->badge()
                    ->color('info'),
                TextColumn::make('district.name')
                    ->label('District')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                TextColumn::make('external_id')
                    ->label('External ID')
                    ->searchable()
                    ->badge()
                    ->color('gray')
                    ->placeholder('-'),
                IconColumn::make('is_active')
                    ->label('Status')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),
                TextColumn::make('prices_count')
                    ->label('Prices')
                    ->counts('prices')
                    ->badge()
                    ->color('success'),
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
                SelectFilter::make('star_rating')
                    ->label('Star Rating')
                    ->options(StarRatingEnum::getOptions()),
                SelectFilter::make('country_id')
                    ->label('Country')
                    ->relationship('country', 'name'),
                SelectFilter::make('province_id')
                    ->label('Province')
                    ->relationship('province', 'name'),
                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name'),
                TernaryFilter::make('is_active')
                    ->label('Status')
                    ->placeholder('All accommodations')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
                SelectFilter::make('has_prices')
                    ->label('Has Prices')
                    ->options([
                        'with_prices' => 'With Prices',
                        'without_prices' => 'Without Prices',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return match ($data['value']) {
                            'with_prices' => $query->has('prices'),
                            'without_prices' => $query->doesntHave('prices'),
                            default => $query,
                        };
                    }),
            ])
            ->defaultSort('name')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
