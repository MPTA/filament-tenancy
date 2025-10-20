<?php

namespace App\Filament\Tenant\Resources\MealTypes\Pages;

use App\Filament\Tenant\Resources\MealTypes\MealTypeResource;
use App\Filament\Shared\Concerns\TranslatableUiLocale;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditMealType extends EditRecord
{
    use Translatable, TranslatableUiLocale {
        TranslatableUiLocale::getDefaultTranslatableLocale insteadof Translatable;
        TranslatableUiLocale::afterSave insteadof Translatable;
        TranslatableUiLocale::mutateFormDataBeforeSave insteadof Translatable;
    }
    protected static string $resource = MealTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
