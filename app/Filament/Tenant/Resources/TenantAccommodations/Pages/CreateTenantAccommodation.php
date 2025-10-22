<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations\Pages;

use App\Filament\Tenant\Resources\TenantAccommodations\TenantAccommodationResource;
use Filament\Resources\Pages\CreateRecord;

class CreateTenantAccommodation extends CreateRecord
{
    protected static string $resource = TenantAccommodationResource::class;
}
