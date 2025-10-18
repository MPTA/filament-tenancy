<?php

namespace App\Filament\App\Widgets;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use App\Models\Tenants\QuotationItinerary;
use Filament\Tables\Table;
use Filament\Tables;
use Filament\Widgets\TableWidget as BaseWidget;

class LatestQuotationItinerariesWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                QuotationItinerary::query()
                    ->with(['quotation.inquiry.contact'])
                    ->latest('updated_at')
                    ->limit(5)
            )
            ->columns([
                Tables\Columns\TextColumn::make('quotation.number')
                    ->label(__('app-quotation-itineraries.columns.number'))
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('quotation.inquiry.title')
                    ->label(__('app-quotation-itineraries.columns.inquiry_title'))
                    ->searchable()
                    ->limit(30),
                    
                Tables\Columns\TextColumn::make('quotation.inquiry.contact.full_name')
                    ->label(__('app-quotation-itineraries.columns.contact'))
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('quotation.expire_date')
                    ->label(__('app-quotation-itineraries.columns.expires'))
                    ->date()
                    ->sortable()
                    ->color(fn ($state) => $state < now() ? 'danger' : ($state < now()->addDays(7) ? 'warning' : 'success')),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label(__('app-quotation-itineraries.columns.updated'))
                    ->dateTime()
                    ->sortable()
                    ->since(),
            ])
            ->recordUrl(fn (QuotationItinerary $record): string => QuotationItineraryResource::getUrl('view', ['record' => $record]))
            ->heading(__('app-quotation-itineraries.widgets.latest_updates'));
    }
}

