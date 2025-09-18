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
            EditAction::make(),
        ];
    }
}
