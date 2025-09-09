<?php

namespace App\Filament\Base\Resources\Accommodations\Pages;

use App\Filament\Base\Resources\Accommodations\AccommodationResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAccommodation extends ViewRecord
{
    protected static string $resource = AccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
