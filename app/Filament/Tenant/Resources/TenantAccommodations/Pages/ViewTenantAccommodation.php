<?php

namespace App\Filament\Tenant\Resources\TenantAccommodations\Pages;

use App\Filament\Tenant\Resources\TenantAccommodations\TenantAccommodationResource;
use Filament\Resources\Pages\ViewRecord;

class ViewTenantAccommodation extends ViewRecord
{
    protected static string $resource = TenantAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            // No header actions needed for read-only accommodations
        ];
    }
}
