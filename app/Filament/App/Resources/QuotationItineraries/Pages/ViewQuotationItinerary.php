<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewQuotationItinerary extends ViewRecord
{
    protected static string $resource = QuotationItineraryResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return 'Quotation ' . ($this->record->quotation?->number ?? 'N/A');
    }

    public function getRecordTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        return $this->record->quotation?->number ?? 'N/A';
    }

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
            'quotation.creator',
            'breakdown.currency',
            'breakdown.companions.companionType',
            'breakdown.vehicleTypes.vehicleType',
            'breakdown.tickets.fromCity',
            'breakdown.tickets.toCity',
            'breakdown.meals.mealType',
            'breakdown.experiences.experience',
            'breakdown.accommodations.accommodation',
            'breakdown.accommodations.city',
            'breakdown.accommodations.rooms.roomCategory',
            'breakdown.attractions.attraction',
            'breakdown.attractions.city',
            'breakdown.attractions.subAttractions.subAttraction',
            'quotationOfferGroups.quotationOffers.vehicleType',
            'quotationOfferGroups.quotationOffers.quotationOfferPrices.roomCategory',
            'quotationOfferGroups.quotationOfferGroupCompanions.companionType',
            'quotationOfferGroups.quotationOfferGroupCompanions.livingCity',
            'quotationOfferGroups.quotationOfferGroupCompanions.roomCategory',
            'quotationOfferGroups.quotationItinerary.breakdown',
            'quotationOfferGroups.quotationItinerary.quotation.currency',
        ]);
    }
}
