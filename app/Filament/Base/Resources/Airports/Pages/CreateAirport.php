<?php

namespace App\Filament\Base\Resources\Airports\Pages;

use App\Filament\Base\Resources\Airports\AirportResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAirport extends CreateRecord
{
    protected static string $resource = AirportResource::class;
}

