<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class CompanionTypesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('tenant-companion-types.columns.companion_type'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(30),
                
                TextColumn::make('companionCategory.name')
                    ->label(__('common-fields.category'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder(__('tenant-companion-types.placeholders.no_category')),
                
                TextColumn::make('nativeLanguage.name')
                    ->label(__('common-fields.native_language'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder(__('tenant-companion-types.placeholders.not_specified')),
                
                TextColumn::make('speakingLanguage.name')
                    ->label(__('common-fields.speaking_language'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder(__('tenant-companion-types.placeholders.not_specified')),
                
                TextColumn::make('per_day_price')
                    ->label(__('common-fields.per_day_price'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-companion-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('per_hour_price')
                    ->label(__('common-fields.per_hour_price'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-companion-types.placeholders.not_set'))
                    ->alignCenter(),
                
                TextColumn::make('slug')
                    ->label(__('common-fields.slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage(__('tenant-companion-types.messages.slug_copied'))
                    ->placeholder(__('tenant-companion-types.placeholders.no_slug')),
                
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
                SelectFilter::make('companion_category_id')
                    ->label(__('common-fields.companion_category'))
                    ->relationship('companionCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('native_language_id')
                    ->label(__('common-fields.native_language'))
                    ->relationship('nativeLanguage', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('speaking_language_id')
                    ->label(__('common-fields.speaking_language'))
                    ->relationship('speakingLanguage', 'name')
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
                        ->label(__('tenant-companion-types.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title(__('tenant-companion-types.notifications.export_started_title'))
                                ->body(__('tenant-companion-types.notifications.export_started_body'))
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('duplicate')
                        ->label(__('tenant-companion-types.bulk_actions.duplicate'))
                        ->icon('heroicon-o-document-duplicate')
                        ->action(function ($records) {
                            foreach ($records as $record) {
                                $newRecord = $record->replicate();
                                $newRecord->name = $record->name . __('tenant-companion-types.copy_suffix');
                                $newRecord->slug = $record->slug . __('tenant-companion-types.copy_slug_suffix');
                                $newRecord->save();
                            }
                            
                            Notification::make()
                                ->title(__('tenant-companion-types.notifications.duplication_complete_title'))
                                ->body(__('tenant-companion-types.notifications.duplication_complete_body'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading(__('tenant-companion-types.empty_state.heading'))
            ->emptyStateDescription(__('tenant-companion-types.empty_state.description'))
            ->defaultSort('name')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
