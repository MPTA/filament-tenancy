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
                    ->label('Companion Type')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->wrap()
                    ->limit(30),
                
                TextColumn::make('companionCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('No category'),
                
                TextColumn::make('nativeLanguage.name')
                    ->label('Native Language')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('Not specified'),
                
                TextColumn::make('speakingLanguage.name')
                    ->label('Speaking Language')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('info')
                    ->placeholder('Not specified'),
                
                TextColumn::make('per_day_price')
                    ->label('Per Day Price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                TextColumn::make('per_hour_price')
                    ->label('Per Hour Price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                TextColumn::make('currency.name')
                    ->label('Currency')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('warning')
                    ->placeholder('Not set'),
                
                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Slug copied')
                    ->placeholder('No slug'),
                
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
                SelectFilter::make('companion_category_id')
                    ->label('Companion Category')
                    ->relationship('companionCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('native_language_id')
                    ->label('Native Language')
                    ->relationship('nativeLanguage', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('speaking_language_id')
                    ->label('Speaking Language')
                    ->relationship('speakingLanguage', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('currency_id')
                    ->label('Currency')
                    ->relationship('currency', 'name')
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
                                ->body('Selected companion types will be exported.')
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
                                ->body('Selected companion types have been duplicated.')
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
