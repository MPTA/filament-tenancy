<?php

namespace App\Filament\App\Resources\Itineraries\Pages;

use App\Filament\App\Resources\Itineraries\ItineraryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewItinerary extends ViewRecord
{
    protected static string $resource = ItineraryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
