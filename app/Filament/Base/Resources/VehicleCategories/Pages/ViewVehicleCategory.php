<?php

namespace App\Filament\Base\Resources\VehicleCategories\Pages;

use App\Filament\Base\Resources\VehicleCategories\VehicleCategoryResource;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewVehicleCategory extends ViewRecord
{
    protected static string $resource = VehicleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make(),
        ];
    }
}
