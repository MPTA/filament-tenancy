<?php

namespace App\Filament\Base\Resources\Attractions\Pages;

use App\Filament\Base\Resources\Attractions\AttractionResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAttraction extends CreateRecord
{
    protected static string $resource = AttractionResource::class;
}
