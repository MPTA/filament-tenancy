<?php

namespace App\Filament\App\Resources\InquiryItineraries\Pages;

use App\Filament\App\Resources\InquiryItineraries\InquiryItineraryResource;
use Filament\Resources\Pages\ListRecords;

class ListInquiryItineraries extends ListRecords
{
    protected static string $resource = InquiryItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action - Inquiries are created through QuotationItinerary
        ];
    }
}
