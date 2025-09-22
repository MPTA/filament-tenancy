<?php

namespace App\Filament\App\Resources\Itineraries\Pages;

use App\Filament\App\Resources\Itineraries\ItineraryResource;
use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use App\Models\Tenants\QuotationItinerary;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditItinerary extends EditRecord
{
    protected static string $resource = ItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }

    protected function getCancelFormAction(): \Filament\Actions\Action
    {
        return parent::getCancelFormAction()
            ->url(function () {
                // Check if this itinerary belongs to a quotation
                if ($this->record->itineraryable_type === QuotationItinerary::class) {
                    $quotationItinerary = $this->record->itineraryable;
                    if ($quotationItinerary?->quotation) {
                        // Redirect to the quotation edit page
                        return QuotationItineraryResource::getUrl('view', ['record' => $quotationItinerary]);
                    }
                }
                
                // Default redirect to itinerary list
                return static::getResource()::getUrl('index');
            });
    }
}
