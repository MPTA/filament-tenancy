<?php

namespace App\Filament\Tenant\Resources\MealTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class MealTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('tenant-meal-types.columns.meal_type'))
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
                    ->copyMessage(__('tenant-meal-types.messages.slug_copied'))
                    ->placeholder(__('tenant-meal-types.placeholders.no_slug')),
                
                TextColumn::make('mealCategory.name')
                    ->label(__('common-fields.category'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder(__('tenant-meal-types.placeholders.no_category')),
                
                TextColumn::make('price')
                    ->label(__('common-fields.price'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-meal-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('description')
                    ->label(__('common-fields.description'))
                    ->searchable()
                    ->limit(50)
                    ->placeholder(__('tenant-meal-types.placeholders.no_description'))
                    ->wrap(),
                
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
                SelectFilter::make('meal_category_id')
                    ->label(__('tenant-meal-types.filters.meal_category'))
                    ->relationship('mealCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('has_price')
                    ->label(__('tenant-meal-types.filters.has_price'))
                    ->options([
                        'yes' => __('tenant-meal-types.filters.has_price_yes'),
                        'no' => __('tenant-meal-types.filters.has_price_no'),
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'yes') {
                            return $query->whereNotNull('price')->where('price', '>', 0);
                        }
                        if ($data['value'] === 'no') {
                            return $query->where(function ($q) {
                                $q->whereNull('price')->orWhere('price', '<=', 0);
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
                        ->label(__('tenant-meal-types.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title(__('tenant-meal-types.notifications.export_started_title'))
                                ->body(__('tenant-meal-types.notifications.export_started_body'))
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('duplicate')
                        ->label(__('tenant-meal-types.bulk_actions.duplicate'))
                        ->icon('heroicon-o-document-duplicate')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $newRecord = $record->replicate();
                                $newRecord->name = $record->name . __('tenant-meal-types.messages.name_copy_suffix');
                                $newRecord->slug = $record->slug . __('tenant-meal-types.messages.slug_copy_suffix');
                                $newRecord->save();
                            }
                            
                            Notification::make()
                                ->title(__('tenant-meal-types.notifications.duplication_complete_title'))
                                ->body(__('tenant-meal-types.notifications.duplication_complete_body'))
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
