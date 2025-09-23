<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuotationItinerary extends ViewRecord
{
    protected static string $resource = QuotationItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // EditAction::make(),
        ];
    }

    protected function resolveRecord(string|int $key): \Illuminate\Database\Eloquent\Model
    {
        $record = parent::resolveRecord($key);
        \Illuminate\Support\Facades\Log::info('ViewQuotationItinerary resolveRecord', [
            'record_id' => $record->id,
            'itinerary_exists' => $record->itinerary ? 'yes' : 'no',
            'itinerary_id' => $record->itinerary?->id
        ]);
        return $record->load([
            'itinerary.days.activities.ticket.toCity',
            'itinerary.days.activities.activityCategory',
            'quotation.currency',
            'quotation.creator'
        ]);
    }
}
