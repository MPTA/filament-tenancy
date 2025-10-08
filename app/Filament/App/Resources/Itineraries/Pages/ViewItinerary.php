<?php

namespace App\Filament\App\Resources\Itineraries\Pages;

use App\Filament\App\Resources\Itineraries\ItineraryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItinerary extends ViewRecord
{
    protected static string $resource = ItineraryResource::class;

    public function getTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $itineraryable = $this->record->itineraryable;
        
        if ($itineraryable instanceof \App\Models\Tenants\QuotationItinerary) {
            return 'Itinerary - Quotation ' . ($itineraryable->quotation?->number ?? 'N/A');
        } elseif ($itineraryable instanceof \App\Models\Tenants\Inquiry) {
            return 'Itinerary - Inquiry ' . ($itineraryable->number ?? $itineraryable->id);
        }
        
        return 'Itinerary';
    }

    public function getRecordTitle(): string|\Illuminate\Contracts\Support\Htmlable
    {
        $itineraryable = $this->record->itineraryable;
        
        if ($itineraryable instanceof \App\Models\Tenants\QuotationItinerary) {
            return 'Quotation ' . ($itineraryable->quotation?->number ?? 'N/A');
        } elseif ($itineraryable instanceof \App\Models\Tenants\Inquiry) {
            return 'Inquiry ' . ($itineraryable->number ?? $itineraryable->id);
        }
        
        return 'Itinerary';
    }

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
