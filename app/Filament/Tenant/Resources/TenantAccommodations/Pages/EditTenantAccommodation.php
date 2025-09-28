<?php

namespace App\Filament\Resources\TenantAccommodations\Pages;

use App\Filament\Resources\TenantAccommodations\TenantAccommodationResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditTenantAccommodation extends EditRecord
{
    protected static string $resource = TenantAccommodationResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
