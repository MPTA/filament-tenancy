<?php

namespace App\Filament\App\Resources\QuotationItineraries\Tables;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class QuotationItinerariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('quotation.number')
                    ->label('Quotation Number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                
                TextColumn::make('quotation.inquiry.title')
                    ->label('Inquiry Title')
                    ->searchable()
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();
                        if (strlen($state) <= $column->getCharacterLimit()) {
                            return null;
                        }
                        return $state;
                    }),
                
                TextColumn::make('quotation.inquiry.contact.full_name')
                    ->label('Contact')
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('quotation.currency.name')
                    ->label('Currency')
                    ->badge()
                    ->color('info'),
                
                TextColumn::make('quotation.exchange_rate')
                    ->label('Exchange Rate')
                    ->numeric(decimalPlaces: 4)
                    ->sortable(),
                
                TextColumn::make('quotation.expire_date')
                    ->label('Expires')
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state < now() ? 'danger' : ($state < now()->addDays(7) ? 'warning' : 'success')),
                
                BadgeColumn::make('quotation.status')
                    ->label('Status')
                    ->colors([
                        'success' => 'Active',
                        'danger' => 'Expired',
                    ]),
                
                TextColumn::make('quotation.inquiry.inquiryItinerary.from_date')
                    ->label('Travel From')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('quotation.inquiry.inquiryItinerary.to_date')
                    ->label('Travel To')
                    ->date()
                    ->sortable()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                
                TextColumn::make('updated_at')
                    ->label('Updated')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('quotation_currency')
                    ->label('Currency')
                    ->relationship('quotation.currency', 'name'),
                
                SelectFilter::make('quotation_status')
                    ->label('Status')
                    ->options([
                        'active' => 'Active',
                        'expired' => 'Expired',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'active') {
                            return $query->whereHas('quotation', function ($q) {
                                $q->where('expire_date', '>', now());
                            });
                        }
                        if ($data['value'] === 'expired') {
                            return $query->whereHas('quotation', function ($q) {
                                $q->where('expire_date', '<=', now());
                            });
                        }
                        return $query;
                    }),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
                    EditAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
