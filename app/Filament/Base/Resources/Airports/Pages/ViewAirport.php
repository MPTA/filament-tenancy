<?php

namespace App\Filament\Base\Resources\Airports\Pages;

use App\Filament\Base\Resources\Airports\AirportResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewAirport extends ViewRecord
{
    protected static string $resource = AirportResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}

