<?php

namespace App\Filament\Base\Resources\Currencies\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Filament\Tables\Actions\HeaderAction;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class CurrenciesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('code')
                    ->label('Code')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->copyable()
                    ->copyMessage('Currency code copied')
                    ->weight('bold'),
                
                TextColumn::make('name')
                    ->label('Currency Name')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->wrap(),
                
                TextColumn::make('symbol')
                    ->label('Symbol')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->placeholder('No symbol')
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
                SelectFilter::make('has_symbol')
                    ->label('Has Symbol')
                    ->options([
                        'yes' => 'Has Symbol',
                        'no' => 'No Symbol',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'yes') {
                            return $query->whereNotNull('symbol')->where('symbol', '!=', '');
                        }
                        if ($data['value'] === 'no') {
                            return $query->where(function ($q) {
                                $q->whereNull('symbol')->orWhere('symbol', '');
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
                                ->body('Selected currencies will be exported.')
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->defaultSort('code')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('30s');
    }
}
