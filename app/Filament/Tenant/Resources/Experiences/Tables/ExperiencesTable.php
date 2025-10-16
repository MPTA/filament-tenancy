<?php

namespace App\Filament\Tenant\Resources\Experiences\Tables;

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

class ExperiencesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('tenant-experiences.columns.experience_name'))
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(40),
                
                TextColumn::make('slug')
                    ->label(__('common-fields.slug'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage(__('tenant-experiences.messages.slug_copied'))
                    ->placeholder(__('tenant-experiences.placeholders.no_slug')),
                
                TextColumn::make('price')
                    ->label(__('common-fields.price'))
                    ->money('USD')
                    ->sortable()
                    ->placeholder(__('tenant-experiences.placeholders.free'))
                    ->alignCenter(),
                
                TextColumn::make('charge_mode')
                    ->label(__('common-fields.charge_mode'))
                    ->badge()
                    ->color('success')
                    ->searchable()
                    ->sortable()
                    ->alignCenter(),
                
                TextColumn::make('city.name')
                    ->label(__('common-fields.city'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder(__('tenant-experiences.placeholders.not_specified')),
                
                TextColumn::make('district.name')
                    ->label(__('common-fields.district'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder(__('tenant-experiences.placeholders.not_specified')),
                
                IconColumn::make('is_active')
                    ->label(__('common-fields.status'))
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger')
                    ->alignCenter(),
                
                TextColumn::make('creator.name')
                    ->label(__('common-fields.created_by'))
                    ->searchable()
                    ->sortable()
                    ->placeholder(__('tenant-experiences.placeholders.unknown')),
                
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
                SelectFilter::make('charge_mode')
                    ->label(__('common-fields.charge_mode'))
                    ->options([
                        'per_person' => __('tenant-experiences.filters.charge_mode.per_person'),
                        'per_group' => __('tenant-experiences.filters.charge_mode.per_group'),
                        'per_hour' => __('tenant-experiences.filters.charge_mode.per_hour'),
                        'fixed' => __('tenant-experiences.filters.charge_mode.fixed'),
                    ])
                    ->searchable(),
                
                SelectFilter::make('city_id')
                    ->label(__('common-fields.city'))
                    ->relationship('city', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('district_id')
                    ->label(__('common-fields.district'))
                    ->relationship('district', 'name')
                    ->searchable()
                    ->preload(),
                
                TernaryFilter::make('is_active')
                    ->label(__('common-fields.status'))
                    ->placeholder(__('tenant-experiences.filters.status.placeholder'))
                    ->trueLabel(__('tenant-experiences.filters.status.true_label'))
                    ->falseLabel(__('tenant-experiences.filters.status.false_label')),
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
                    
                    BulkAction::make('activate')
                        ->label(__('tenant-experiences.bulk_actions.activate'))
                        ->icon('heroicon-o-check-circle')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => true]);
                            
                            Notification::make()
                                ->title(__('tenant-experiences.notifications.activated_title'))
                                ->body(__('tenant-experiences.notifications.activated_body'))
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('deactivate')
                        ->label(__('tenant-experiences.bulk_actions.deactivate'))
                        ->icon('heroicon-o-x-circle')
                        ->action(function ($records) {
                            $records->each->update(['is_active' => false]);
                            
                            Notification::make()
                                ->title(__('tenant-experiences.notifications.deactivated_title'))
                                ->body(__('tenant-experiences.notifications.deactivated_body'))
                                ->success()
                                ->send();
                        }),
                    
                    BulkAction::make('export')
                        ->label(__('tenant-experiences.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title(__('tenant-experiences.notifications.export_started_title'))
                                ->body(__('tenant-experiences.notifications.export_started_body'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading(__('tenant-experiences.empty_state.heading'))
            ->emptyStateDescription(__('tenant-experiences.empty_state.description'))
            ->defaultSort('name')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
