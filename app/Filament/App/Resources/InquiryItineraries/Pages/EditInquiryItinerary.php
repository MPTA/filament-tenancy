<?php

namespace App\Filament\App\Resources\InquiryItineraries\Pages;

use App\Filament\App\Resources\InquiryItineraries\InquiryItineraryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInquiryItinerary extends EditRecord
{
    protected static string $resource = InquiryItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
