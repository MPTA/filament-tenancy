<?php

namespace App\Filament\Tenant\Resources\VehicleTypes\Pages;

use App\Filament\Shared\Concerns\TranslatableUiLocale;
use App\Filament\Tenant\Resources\VehicleTypes\VehicleTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditVehicleType extends EditRecord
{
    use Translatable, TranslatableUiLocale {
        TranslatableUiLocale::getDefaultTranslatableLocale insteadof Translatable;
        TranslatableUiLocale::afterSave insteadof Translatable;
        TranslatableUiLocale::mutateFormDataBeforeSave insteadof Translatable;
    }
    protected static string $resource = VehicleTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
