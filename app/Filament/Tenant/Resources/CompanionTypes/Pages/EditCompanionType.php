<?php

namespace App\Filament\Tenant\Resources\CompanionTypes\Pages;

use App\Filament\Shared\Concerns\TranslatableUiLocale;
use App\Filament\Tenant\Resources\CompanionTypes\CompanionTypeResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use LaraZeus\SpatieTranslatable\Resources\Pages\EditRecord\Concerns\Translatable;

class EditCompanionType extends EditRecord
{
    use Translatable, TranslatableUiLocale {
        TranslatableUiLocale::getDefaultTranslatableLocale insteadof Translatable;
        TranslatableUiLocale::afterSave insteadof Translatable;
        TranslatableUiLocale::mutateFormDataBeforeSave insteadof Translatable;
    }
    protected static string $resource = CompanionTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make(),
            DeleteAction::make(),
        ];
    }
}
