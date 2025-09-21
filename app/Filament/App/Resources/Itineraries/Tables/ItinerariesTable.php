<?php

namespace App\Filament\App\Resources\Itineraries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItinerariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->label('ID'),
                TextColumn::make('tenant.name')
                    ->searchable(),
                TextColumn::make('travel_mode')
                    ->badge()
                    ->searchable(),
                IconColumn::make('is_advanced')
                    ->boolean(),
                IconColumn::make('is_complete')
                    ->boolean(),
                IconColumn::make('is_vip')
                    ->boolean(),
                TextColumn::make('creator_user_id'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('itineraryable_type')
                    ->searchable(),
                TextColumn::make('itineraryable_id'),
            ])
            ->filters([
                //
            ])
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
