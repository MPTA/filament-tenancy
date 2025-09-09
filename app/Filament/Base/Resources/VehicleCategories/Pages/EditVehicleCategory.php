<?php

namespace App\Filament\Base\Resources\VehicleCategories\Pages;

use App\Filament\Base\Resources\VehicleCategories\VehicleCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditVehicleCategory extends EditRecord
{
    use Translatable;
    protected static string $resource = VehicleCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
