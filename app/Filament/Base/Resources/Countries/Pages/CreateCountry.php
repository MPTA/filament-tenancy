<?php

namespace App\Filament\Base\Resources\Countries\Pages;

use App\Filament\Base\Resources\Countries\CountryResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCountry extends CreateRecord
{
    protected static string $resource = CountryResource::class;
}
