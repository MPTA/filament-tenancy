<?php

namespace App\Filament\Base\Resources\Accommodations\Pages;

use App\Filament\Base\Resources\Accommodations\AccommodationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccommodation extends CreateRecord
{
    protected static string $resource = AccommodationResource::class;
}
