<?php

namespace App\Filament\Base\Resources\VehicleCategories\Pages;

use App\Filament\Base\Resources\VehicleCategories\VehicleCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListVehicleCategories extends ListRecords
{
    protected static string $resource = VehicleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
