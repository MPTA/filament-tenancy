<?php

namespace App\Filament\Tenant\Resources\VehicleTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class VehicleTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Vehicle Type')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(30),
                
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Slug copied')
                    ->placeholder('No slug'),
                
                TextColumn::make('vehicleCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('No category'),
                
                TextColumn::make('capacity_from')
                    ->label('Min Capacity')
                    ->numeric()
                    ->sortable()
                    ->suffix(' pax')
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                TextColumn::make('capacity_to')
                    ->label('Max Capacity')
                    ->numeric()
                    ->sortable()
                    ->suffix(' pax')
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                TextColumn::make('per_day_price')
                    ->label('Per Day Price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                TextColumn::make('half_day_price')
                    ->label('Half Day Price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                IconColumn::make('is_vip')
                    ->label('VIP')
                    ->boolean()
                    ->trueIcon('heroicon-o-star')
                    ->falseIcon('heroicon-o-star')
                    ->trueColor('warning')
                    ->falseColor('gray')
                    ->alignCenter(),
                
                TextColumn::make('airport_transfer_price')
                    ->label('Airport Transfer')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Not set')
                    ->alignCenter(),
                
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
                SelectFilter::make('vehicle_category_id')
                    ->label('Vehicle Category')
                    ->relationship('vehicleCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                TernaryFilter::make('is_vip')
                    ->label('VIP Service')
                    ->placeholder('All vehicles')
                    ->trueLabel('VIP only')
                    ->falseLabel('Non-VIP only'),
                
                SelectFilter::make('capacity_range')
                    ->label('Capacity Range')
                    ->options([
                        '1-4' => '1-4 passengers',
                        '5-8' => '5-8 passengers',
                        '9-16' => '9-16 passengers',
                        '17-30' => '17-30 passengers',
                        '30+' => '30+ passengers',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === '1-4') {
                            return $query->where('capacity_from', '<=', 4)->where('capacity_to', '>=', 1);
                        }
                        if ($data['value'] === '5-8') {
                            return $query->where('capacity_from', '<=', 8)->where('capacity_to', '>=', 5);
                        }
                        if ($data['value'] === '9-16') {
                            return $query->where('capacity_from', '<=', 16)->where('capacity_to', '>=', 9);
                        }
                        if ($data['value'] === '17-30') {
                            return $query->where('capacity_from', '<=', 30)->where('capacity_to', '>=', 17);
                        }
                        if ($data['value'] === '30+') {
                            return $query->where('capacity_to', '>', 30);
                        }
                        return $query;
                    }),
                
                SelectFilter::make('has_pricing')
                    ->label('Has Pricing')
                    ->options([
                        'yes' => 'Has Pricing',
                        'no' => 'No Pricing',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'yes') {
                            return $query->where(function ($q) {
                                $q->whereNotNull('per_day_price')->where('per_day_price', '>', 0)
                                  ->orWhereNotNull('half_day_price')->where('half_day_price', '>', 0)
                                  ->orWhereNotNull('airport_transfer_price')->where('airport_transfer_price', '>', 0);
                            });
                        }
                        if ($data['value'] === 'no') {
                            return $query->where(function ($q) {
                                $q->where(function ($subQ) {
                                    $subQ->whereNull('per_day_price')->orWhere('per_day_price', '<=', 0);
                                })->where(function ($subQ) {
                                    $subQ->whereNull('half_day_price')->orWhere('half_day_price', '<=', 0);
                                })->where(function ($subQ) {
                                    $subQ->whereNull('airport_transfer_price')->orWhere('airport_transfer_price', '<=', 0);
                                });
                            });
                        }
                        return $query;
                    }),
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
                                ->body('Selected vehicle types will be exported.')
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('duplicate')
                        ->label('Duplicate Selected')
                        ->icon('heroicon-o-document-duplicate')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $newRecord = $record->replicate();
                                $newRecord->name = $record->name . ' (Copy)';
                                $newRecord->slug = $record->slug . '-copy';
                                $newRecord->save();
                            }
                            
                            Notification::make()
                                ->title('Duplication Complete')
                                ->body('Selected vehicle types have been duplicated.')
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('toggle_vip')
                        ->label('Toggle VIP Status')
                        ->icon('heroicon-o-star')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_vip' => !$record->is_vip]);
                            }
                            
                            Notification::make()
                                ->title('VIP Status Updated')
                                ->body('Selected vehicle types VIP status has been toggled.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('name')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
