<?php

namespace App\Filament\App\Resources\Itineraries\Tables;

use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ItinerariesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with([
                'itineraryable.quotation.inquiry.contact',
                'days',
            ]))
            ->columns([
                TextColumn::make('quotation_number')
                    ->label('Quotation Number')
                    ->weight('bold')
                    ->state(function ($record) {
                        if ($record->itineraryable_type === QuotationItinerary::class && $record->itineraryable) {
                            return $record->itineraryable->quotation?->number ?? '—';
                        }
                        return '—';
                    })
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('inquiry_title')
                    ->label('Inquiry Title')
                    ->limit(50)
                    ->state(function ($record) {
                        if ($record->itineraryable_type === QuotationItinerary::class && $record->itineraryable) {
                            return $record->itineraryable->quotation?->inquiry?->title ?? '—';
                        }
                        return '—';
                    })
                    ->searchable()
                    ->tooltip(function ($record) {
                        if ($record->itineraryable_type === QuotationItinerary::class && $record->itineraryable) {
                            $title = $record->itineraryable->quotation?->inquiry?->title ?? '';
                            if (strlen($title) > 50) {
                                return $title;
                            }
                        }
                        return null;
                    }),
                
                TextColumn::make('contact')
                    ->label('Contact')
                    ->state(function ($record) {
                        if ($record->itineraryable_type === QuotationItinerary::class && $record->itineraryable) {
                            return $record->itineraryable->quotation?->inquiry?->contact?->full_name ?? '—';
                        }
                        return '—';
                    })
                    ->searchable()
                    ->sortable(),
                
                TextColumn::make('days_count')
                    ->label('Days')
                    ->state(fn ($record) => $record->days()->count())
                    ->badge()
                    ->color('info')
                    ->alignCenter(),
                
                TextColumn::make('travel_mode')
                    ->label('Travel Mode')
                    ->badge()
                    ->toggleable(),
                
                TextColumn::make('created_at')
                    ->label('Created')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordUrl(function ($record) {
                // کلیک روی رکورد → باز کردن QuotationItinerary با تب itinerary
                if ($record->itineraryable_type === QuotationItinerary::class && $record->itineraryable) {
                    return route('filament.app.resources.quotation-itineraries.view', [
                        'record' => $record->itineraryable->id,
                    ]);
                }
                return null;
            })
            ->recordActions([
                Action::make('view')
                    ->label('View')
                    ->icon('heroicon-o-eye')
                    ->color('primary')
                    ->url(function ($record) {
                        if ($record->itineraryable_type === QuotationItinerary::class && $record->itineraryable) {
                            return route('filament.app.resources.quotation-itineraries.view', [
                                'record' => $record->itineraryable->id,
                            ]);
                        }
                        return null;
                    })
                    ->disabled(fn ($record) => !$record->itineraryable || $record->itineraryable_type !== QuotationItinerary::class),
            ])
            ->toolbarActions([
                // هیچ toolbar action ای نیست
            ])
            ->defaultSort('created_at', 'desc')
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
