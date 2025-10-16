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
                    ->label(__('tenant-vehicle-types.columns.vehicle_type'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(30),
                
                TextColumn::make('slug')
                    ->label(__('common-fields.slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage(__('tenant-vehicle-types.messages.slug_copied'))
                    ->placeholder(__('tenant-vehicle-types.placeholders.no_slug')),
                
                TextColumn::make('vehicleCategory.name')
                    ->label(__('common-fields.category'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder(__('tenant-vehicle-types.placeholders.no_category')),
                
                TextColumn::make('capacity_from')
                    ->label(__('tenant-vehicle-types.fields.min_capacity'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ' . __('tenant-vehicle-types.suffixes.pax'))
                    ->placeholder(__('tenant-vehicle-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('capacity_to')
                    ->label(__('tenant-vehicle-types.fields.max_capacity'))
                    ->numeric()
                    ->sortable()
                    ->suffix(' ' . __('tenant-vehicle-types.suffixes.pax'))
                    ->placeholder(__('tenant-vehicle-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('per_day_price')
                    ->label(__('tenant-vehicle-types.fields.per_day_price'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-vehicle-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('half_day_price')
                    ->label(__('tenant-vehicle-types.fields.half_day_price'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-vehicle-types.placeholders.not_set'))
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
                    ->label(__('tenant-vehicle-types.columns.airport_transfer'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-vehicle-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('created_at')
                    ->label(__('common-fields.created_at'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                
                TextColumn::make('updated_at')
                    ->label(__('common-fields.updated_at'))
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('vehicle_category_id')
                    ->label(__('tenant-vehicle-types.filters.vehicle_category'))
                    ->relationship('vehicleCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                TernaryFilter::make('is_vip')
                    ->label(__('tenant-vehicle-types.filters.vip_service'))
                    ->placeholder(__('tenant-vehicle-types.filters.all_vehicles'))
                    ->trueLabel(__('tenant-vehicle-types.filters.vip_only'))
                    ->falseLabel(__('tenant-vehicle-types.filters.non_vip_only')),
                
                SelectFilter::make('capacity_range')
                    ->label(__('tenant-vehicle-types.filters.capacity_range'))
                    ->options([
                        '1-4' => __('tenant-vehicle-types.filters.capacity_1_4'),
                        '5-8' => __('tenant-vehicle-types.filters.capacity_5_8'),
                        '9-16' => __('tenant-vehicle-types.filters.capacity_9_16'),
                        '17-30' => __('tenant-vehicle-types.filters.capacity_17_30'),
                        '30+' => __('tenant-vehicle-types.filters.capacity_30_plus'),
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
                    ->label(__('tenant-vehicle-types.filters.has_pricing'))
                    ->options([
                        'yes' => __('tenant-vehicle-types.filters.has_pricing_yes'),
                        'no' => __('tenant-vehicle-types.filters.has_pricing_no'),
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
                        ->label(__('tenant-vehicle-types.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title(__('tenant-vehicle-types.notifications.export_started_title'))
                                ->body(__('tenant-vehicle-types.notifications.export_started_body'))
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('duplicate')
                        ->label(__('tenant-vehicle-types.bulk_actions.duplicate'))
                        ->icon('heroicon-o-document-duplicate')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $newRecord = $record->replicate();
                                $newRecord->name = $record->name . __('tenant-vehicle-types.messages.name_copy_suffix');
                                $newRecord->slug = $record->slug . __('tenant-vehicle-types.messages.slug_copy_suffix');
                                $newRecord->save();
                            }
                            
                            Notification::make()
                                ->title(__('tenant-vehicle-types.notifications.duplication_complete_title'))
                                ->body(__('tenant-vehicle-types.notifications.duplication_complete_body'))
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('toggle_vip')
                        ->label(__('tenant-vehicle-types.bulk_actions.toggle_vip'))
                        ->icon('heroicon-o-star')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $record->update(['is_vip' => !$record->is_vip]);
                            }
                            
                            Notification::make()
                                ->title(__('tenant-vehicle-types.notifications.vip_status_updated_title'))
                                ->body(__('tenant-vehicle-types.notifications.vip_status_updated_body'))
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
