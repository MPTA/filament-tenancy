<?php

namespace App\Filament\Tenant\Resources\VehicleTypes\Pages;

use App\Filament\Tenant\Resources\VehicleTypes\VehicleTypeResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicleType extends ViewRecord
{
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
