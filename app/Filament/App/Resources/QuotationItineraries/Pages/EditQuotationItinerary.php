<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditQuotationItinerary extends EditRecord
{
    protected static string $resource = QuotationItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
