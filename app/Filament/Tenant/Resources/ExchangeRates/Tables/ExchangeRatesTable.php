<?php

namespace App\Filament\Tenant\Resources\ExchangeRates\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Actions\BulkAction;
use Filament\Notifications\Notification;

class ExchangeRatesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('fromCurrency.name')
                    ->label(__('common-fields.from_currency'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('primary')
                    ->weight('bold'),
                
                TextColumn::make('toCurrency.name')
                    ->label(__('common-fields.to_currency'))
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('success')
                    ->weight('bold'),
                
                TextColumn::make('rate')
                    ->label(__('common-fields.exchange_rate'))
                    ->searchable()
                    ->sortable()
                    ->numeric(
                        decimalPlaces: 6,
                        decimalSeparator: '.',
                        thousandsSeparator: ',',
                    )
                    ->weight('medium')
                    ->alignCenter(),
                
                TextColumn::make('created_at')
                    ->label(__('common-fields.created_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
                
                TextColumn::make('updated_at')
                    ->label(__('common-fields.updated_at'))
                    ->dateTime('M j, Y g:i A')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->alignCenter(),
            ])
            ->filters([
                SelectFilter::make('from_currency_id')
                    ->label(__('common-fields.from_currency'))
                    ->relationship('fromCurrency', 'name')
                    ->searchable()
                    ->preload(),
                
                SelectFilter::make('to_currency_id')
                    ->label(__('common-fields.to_currency'))
                    ->relationship('toCurrency', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                EditAction::make()
                    ->icon('heroicon-o-pencil'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()
                        ->requiresConfirmation(),
                    
                    BulkAction::make('export')
                        ->label(__('tenant-exchange-rates.bulk_actions.export'))
                        ->icon('heroicon-o-arrow-down-tray')
                        ->action(function ($records) {
                            // Export logic can be implemented here
                            Notification::make()
                                ->title(__('tenant-exchange-rates.notifications.export_started_title'))
                                ->body(__('tenant-exchange-rates.notifications.export_started_body'))
                                ->success()
                                ->send();
                        }),
                ]),
            ])
            ->emptyStateHeading(__('tenant-exchange-rates.empty_state.heading'))
            ->emptyStateDescription(__('tenant-exchange-rates.empty_state.description'))
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100])
            ->poll('60s');
    }
}
