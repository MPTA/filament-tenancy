<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Pages;

use App\Filament\Tenant\Resources\CompanionTypes\CompanionTypeResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCompanionType extends CreateRecord
{
    protected static string $resource = CompanionTypeResource::class;
}
