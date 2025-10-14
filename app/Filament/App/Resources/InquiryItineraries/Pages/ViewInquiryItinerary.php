<?php

namespace App\Filament\App\Resources\InquiryItineraries\Pages;

use App\Filament\App\Resources\InquiryItineraries\InquiryItineraryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInquiryItinerary extends ViewRecord
{
    protected static string $resource = InquiryItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
