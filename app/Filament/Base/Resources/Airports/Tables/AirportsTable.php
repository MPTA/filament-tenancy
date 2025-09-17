<?php

namespace App\Filament\Base\Resources\Airports\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class AirportsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('iata_code')
                    ->label('IATA Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('IATA code copied')
                    ->weight('bold')
                    ->alignCenter(),
                
                TextColumn::make('name')
                    ->label('Airport Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap()
                    ->limit(50),
                
                TextColumn::make('city.name')
                    ->label('City')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No city')
                    ->icon('heroicon-o-map-pin'),
                
                TextColumn::make('city.province.name')
                    ->label('Province/State')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No province')
                    ->icon('heroicon-o-building-office'),
                
                TextColumn::make('city.province.country.name')
                    ->label('Country')
                    ->searchable()
                    ->sortable()
                    ->placeholder('No country')
                    ->icon('heroicon-o-flag'),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('city_id')
                    ->label('City')
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('city.province_id')
                    ->label('Province/State')
                    ->relationship('city.province', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('city.province.country_id')
                    ->label('Country')
                    ->relationship('city.province.country', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make()
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    
                    BulkAction::make('export')
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title('Export Started')
                                ->body('Selected airports will be exported.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('iata_code')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}

