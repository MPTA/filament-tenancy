<?php

namespace App\Filament\App\Resources\Itineraries\Pages;

use App\Filament\App\Resources\Itineraries\ItineraryResource;
use Filament\Resources\Pages\ListRecords;

class ListItineraries extends ListRecords
{
    protected static string $resource = ItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No create action
        ];
    }
}
