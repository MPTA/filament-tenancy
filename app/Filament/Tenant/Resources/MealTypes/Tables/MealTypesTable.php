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
                    ->label('Meal Type')
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
                
                TextColumn::make('mealCategory.name')
                    ->label('Category')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('No category'),
                
                TextColumn::make('price')
                    ->label('Price')
                    ->money('USD')
                    ->sortable()
                    ->placeholder('Not set')
                    ->alignCenter(),
                
                TextColumn::make('description')
                    ->label('Description')
                    ->searchable()
                    ->limit(50)
                    ->placeholder('No description')
                    ->wrap(),
                
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
                SelectFilter::make('meal_category_id')
                    ->label('Meal Category')
                    ->relationship('mealCategory', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('has_price')
                    ->label('Has Price')
                    ->options([
                        'yes' => 'Has Price',
                        'no' => 'No Price',
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
                        ->label('Export Selected')
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title('Export Started')
                                ->body('Selected meal types will be exported.')
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
                                ->body('Selected meal types have been duplicated.')
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
