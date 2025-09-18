<?php

namespace App\Filament\App\Resources\QuotationItineraries\Pages;

use App\Filament\App\Resources\QuotationItineraries\QuotationItineraryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotationItineraries extends ListRecords
{
    protected static string $resource = QuotationItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
