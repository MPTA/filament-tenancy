<?php

namespace App\Filament\Base\Resources\Attractions\Tables;

use App\Enums\AttractionTypeEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;

class AttractionsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->limit(30),
                TextColumn::make('type')
                    ->badge()
                    ->color(fn ($state): string => match ($state?->value ?? $state) {
                        'natural' => 'success',
                        'man_made' => 'info',
                        'cultural' => 'warning',
                        'sport' => 'danger',
                        'events' => 'primary',
                        'leisure' => 'gray',
                        default => 'gray',
                    })
                    ->formatStateUsing(fn ($state): string => $state instanceof AttractionTypeEnum ? $state->label() : AttractionTypeEnum::from($state)->label()),
                TextColumn::make('location')
                    ->getStateUsing(function ($record) {
                        $parts = array_filter([
                            $record->city?->name,
                            $record->country?->name,
                        ]);
                        return implode(', ', $parts);
                    })
                    ->sortable(false),
                TextColumn::make('rating')
                    ->numeric()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? number_format($state, 1) . '/5' : '-')
                    ->color(fn ($state) => $state >= 4 ? 'success' : ($state >= 3 ? 'warning' : 'gray')),
                TextColumn::make('local_price')
                    ->money(fn ($record) => $record->country?->currency?->code ?? 'USD')
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('foreigner_price')
                    ->money(fn ($record) => $record->country?->currency?->code ?? 'USD')
                    ->sortable()
                    ->toggleable(),
                IconColumn::make('is_active')
                    ->boolean()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->options(AttractionTypeEnum::getOptions())
                    ->multiple(),
                SelectFilter::make('country_id')
                    ->relationship('country', 'name')
                    ->searchable()
                    ->preload(),
                SelectFilter::make('city_id')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                TernaryFilter::make('is_active')
                    ->label('Active Status')
                    ->placeholder('All attractions')
                    ->trueLabel('Active only')
                    ->falseLabel('Inactive only'),
            ])
            ->defaultSort('created_at', 'desc')
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
