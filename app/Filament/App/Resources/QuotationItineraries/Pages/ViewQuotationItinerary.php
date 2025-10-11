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
            // Itinerary and days with all activity relationships
            'itinerary.days.currentCity',
            'itinerary.days.accommodationCity',
            'itinerary.days.accommodation',
            'itinerary.days.activities.activityCategory',
            'itinerary.days.activities.city',
            // Meal activities
            'itinerary.days.activities.meal.mealType',
            // Attraction activities with nested relationships
            // Note: Attraction model auto-loads 'country.currency' via $with property
            'itinerary.days.activities.attraction.attraction',
            'itinerary.days.activities.attraction.subAttractions.subAttraction',
            // Ticket activities
            'itinerary.days.activities.ticket.toCity',
            // Experience activities
            'itinerary.days.activities.experience.experience.city',
            // Quotation relationships
            'quotation.currency',
            'quotation.creator',
            'quotation.inquiry.contact',
            'quotation.inquiry.requestedCurrency',
            'quotation.inquiry.inquiryItinerary',
            // Breakdown relationships
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
            // Note: Attraction model auto-loads 'country.currency' via $with property
            'breakdown.attractions.attraction',
            'breakdown.attractions.city',
            'breakdown.attractions.subAttractions.subAttraction',
            // Quotation offer groups
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
